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

namespace Markocupic\ExportTable\Writer;

use Contao\CoreBundle\Exception\ResponseException;
use Contao\File;
use Haste\IO\Reader\ArrayReader;
use Haste\IO\Writer\CsvFileWriter;
use Markocupic\ExportTable\Config\Config;
use Symfony\Component\HttpFoundation\Response;

class CsvWriter implements WriterInterface
{
    /**
     * @throws \Exception
     */
    public function write(array $arrData, Config $config)
    {
        $targetFolder = $config->getTargetFolder() ?? $config->getTempFolder();

        // Use codefog haste and its ArrayReader- and CsvFileWriter-class
        $objReader = new ArrayReader($arrData);

        $strFilename = $config->getFilename() ?? $config->getTable().'.csv';
        $targetFolder = $config->getTargetFolder() ?? $config->getTempFolder();

        $objWriter = new CsvFileWriter($targetFolder.'/'.$strFilename);
        $objWriter->setDelimiter($config->getDelimiter());
        $objWriter->setEnclosure($config->getEnclosure());

        // Write content into a file
        $objWriter->writeFrom($objReader);

        // Send the created file to the browser
        $objFile = new File($objWriter->getFilename());

        $response = new Response($objFile->sendToBrowser());

        return new ResponseException($response);
    }
}
