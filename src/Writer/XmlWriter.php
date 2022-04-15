<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Writer;

use Contao\File;
use Markocupic\ExportTable\Config\Config;

class XmlWriter extends AbstractWriter implements WriterInterface
{
    public const FILE_ENDING = 'xml';

    /**
     * @throws \Exception
     */
    public function write(array $arrData, Config $objConfig): void
    {
        // Run pre-write HOOK: e.g. modify the data array
        $arrData = $this->runPreWriteHook($arrData, $objConfig);

        $objXml = new \XMLWriter();
        $objXml->openMemory();
        $objXml->setIndent(true);
        $objXml->setIndentString("\t");
        $objXml->startDocument('1.0', 'UTF-8');
        $objXml->startElement($objConfig->getTable());

        foreach ($arrData as $arrRow) {
            // Add a new row
            $objXml->startElement('datarecord');

            foreach ($arrRow as $fieldName => $fieldValue) {
                // Add a field
                $objXml->startElement($fieldName);

                $fieldValue = (string) $fieldValue;

                if (is_numeric($fieldValue) || null === $fieldValue || '' === $fieldValue) {
                    $objXml->text($fieldValue);
                } else {
                    // Write CDATA
                    $objXml->writeCdata($fieldValue);
                }

                // Add the closing field tag
                $objXml->endElement();
            }
            // Add the closing row tag
            $objXml->endElement();
        }

        // Add the closing table tag
        $objXml->endElement();

        // Add the closing document tag
        $objXml->endDocument();

        // Write output to the file system
        $targetPath = $this->getTargetPath($objConfig, self::FILE_ENDING);

        $objFile = new File($targetPath);
        $objFile->write($objXml->outputMemory());
        $objFile->close();

        // Run post-write HOOK: e.g. send notifications, etc.
        $objFile = $this->runPostWriteHook($objFile, $objConfig);

        $this->log($objFile, $objConfig);

        if ($objConfig->getSendFileToTheBrowser()) {
            // Show the download dialog
            $this->sendFileToBrowser($objFile);
        }

        $this->sendBackendMessage($objFile);
    }
}
