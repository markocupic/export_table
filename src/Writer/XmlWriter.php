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
use Markocupic\ExportTable\Config\Config;
use Symfony\Component\HttpFoundation\Response;

class XmlWriter implements WriterInterface
{
    public function write(array $arrData, Config $config)
    {

        $objXml = new \XMLWriter();
        $objXml->openMemory();
        $objXml->setIndent(true);
        $objXml->setIndentString("\t");
        $objXml->startDocument('1.0', 'UTF-8');

        $objXml->startElement($config->getTable());

        foreach ($arrData as $row => $arrRow) {
            // Headline
            if (0 === $row) {
                continue;
            }

            // New row
            $objXml->startElement('datarecord');

            foreach ($arrRow as $i => $fieldvalue) {
                // New field
                $objXml->startElement($arrData[0][$i]);

                if (is_numeric($fieldvalue) || null === $fieldvalue || '' === $fieldvalue) {
                    $objXml->text($fieldvalue);
                } else {
                    // Write CDATA
                    $objXml->writeCdata($fieldvalue);
                }

                //end field-tag
                $objXml->endElement();
            }
            // End row-tag
            $objXml->endElement();
        }
        // End table-tag
        $objXml->endElement();

        // End document
        $objXml->endDocument();

        // Write output to file system
        $strFilename = $config->getFilename() ?? $config->getTable().'.xml';
        $targetFolder = $config->getTargetFolder() ?? $config->getTempFolder();

        $objFile = new File($targetFolder.'/'.$strFilename);
        $objFile->write($objXml->outputMemory());
        $objFile->close();

        return $objFile->sendToBrowser();
    }
}