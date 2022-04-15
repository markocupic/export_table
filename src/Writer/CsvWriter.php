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
use Haste\IO\Reader\ArrayReader;
use Haste\IO\Writer\CsvFileWriter;
use Markocupic\ExportTable\Config\Config;

class CsvWriter extends AbstractWriter implements WriterInterface
{
    public const FILE_ENDING = 'csv';

    /**
     * @throws \Exception
     */
    public function write(array $arrData, Config $objConfig): void
    {
        // Run pre-write HOOK: e.g. modify the data array
        $arrData = $this->runPreWriteHook($arrData, $objConfig);

        // Use codefog haste and its ArrayReader- and CsvFileWriter-class
        $objReader = new ArrayReader($arrData);

        // Write content into a file
        $targetPath = $this->getTargetPath($objConfig, self::FILE_ENDING);
        $objWriter = new CsvFileWriter($targetPath);

        if ($objConfig->getAddHeadline() && !empty($objConfig->getHeadlineFields())) {
            $objWriter->enableHeaderFields();
            $objReader->setHeaderFields($objConfig->getHeadlineFields());
        }

        $objWriter->setDelimiter($objConfig->getDelimiter());
        $objWriter->setEnclosure($objConfig->getEnclosure());
        $objWriter->writeFrom($objReader);

        // Send generated file to the browser
        $objFile = new File($objWriter->getFilename());

        if ($objConfig->getBom()) {
            $objFile = $this->addBom($objFile, $objConfig->getBom());
        }

        // Run post-write HOOK: e.g. send notifications, etc.
        $objFile = $this->runPostWriteHook($objFile, $objConfig);

        $this->sendFileToBrowser($objFile, false);
    }
}
