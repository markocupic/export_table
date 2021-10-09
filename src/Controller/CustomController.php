<?php

declare(strict_types=1);

/*
 * This file is part of Export Table for Contao CMS.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Export\ExportTable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/_test_export_foo_bar", name="_test_export_foo_bar", defaults={"_scope" = "frontend", "_token_check" = false})
 */
class CustomController extends AbstractController
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var ExportTable
     */
    private $exportTable;

    public function __construct(ContaoFramework $framework, ExportTable $exportTable)
    {
        $this->framework = $framework;
        $this->exportTable = $exportTable;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(): Response
    {
        die('This is custom controller for educational purposes');

        $this->framework->initialize();

        $config = (new Config())
            ->setExportType('xml')
            ->setTable('tl_member')
            ->setFields(['firstname', 'lastname', 'dateOfBirth'])
            ->setDelimiter(',')
            ->setEnclosure('"')
            ->setFilter('[["city=?"],["Oberkirch"]]')
            // Define a target path, otherwise the file will be stored in system/tmp
            ->setTargetFolder('files')
            // Define a filename, otherwise the file will become the name of the table ->tl_member.csv
            ->setFilename('export.csv')
        ;

        return $this->exportTable->run($config);
    }
}
