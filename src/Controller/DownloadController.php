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
use Markocupic\ExportTable\Config\GetConfigFromToken;
use Markocupic\ExportTable\Export\ExportTable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/_export_table_download_table", name="export_table_download_table", defaults={"_scope" = "frontend", "_token_check" = false})
 */
class DownloadController extends AbstractController
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var GetConfigFromUrl
     */
    private $setConfigFromUrl;

    /**
     * @var ExportTable
     */
    private $exportTable;

    public function __construct(ContaoFramework $framework, RequestStack $requestStack, GetConfigFromToken $getConfigFromToken, ExportTable $exportTable)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
        $this->getConfigFromToken = $getConfigFromToken;
        $this->exportTable = $exportTable;
    }

    public function __invoke(): Response
    {
        $this->framework->initialize();
        $request = $this->requestStack->getCurrentRequest();
        $strToken = $request->query->get('key');
        
        $objConfig = $this->getConfigFromToken->get($strToken);
        return $this->exportTable->exportTable($objConfig);
    }
}
