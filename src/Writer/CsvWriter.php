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
use Contao\FilesModel;
use Haste\IO\Reader\ArrayReader;
use Haste\IO\Writer\CsvFileWriter;
use Markocupic\ExportTable\Config\Config;

class CsvWriter extends AbstractWriter implements WriterInterface
{
    public const FILE_ENDING = 'csv';

    /**
     * @throws \Exception
     */
    public function write(array $arrData, Config $config): void
    {
        $filesModelAdapter = $this->framework->getAdapter(FilesModel::class);

        // Run pre-write HOOK: e.g. modify the data array
        $arrData = $this->runPreWriteHook($arrData, $config);

        // Use codefog/haste and its ArrayReader- and CsvFileWriter-class
        $objReader = new ArrayReader($arrData);

        // Write content into a file
        $targetPath = $this->getTargetPath($config, self::FILE_ENDING);
        $objWriter = new CsvFileWriter($targetPath);

        if ($config->getAddHeadline() && !empty($config->getHeadlineFields())) {
            $objWriter->enableHeaderFields();
            $objReader->setHeaderFields($config->getHeadlineFields());
        }

        $objWriter->setDelimiter($config->getDelimiter());
        $objWriter->setEnclosure($config->getEnclosure());
        $objWriter->writeFrom($objReader);

        // Send generated file to the browser
        $objFile = new File($objWriter->getFilename());

        if ($config->getOutputBom()) {
            $objFile = $this->setOutputBom($objFile, $config->getOutputBom());
        }

        // Run post-write HOOK: e.g. send notifications, etc.
        $objFile = $this->runPostWriteHook($objFile, $config);

        if ($config->getSaveExport() && $config->getSaveExportDirectory() && null !== $filesModelAdapter->findByUuid($config->getSaveExportDirectory())) {
            // Save file to filesystem
            $this->sendBackendMessage($objFile);
        } else {
            // Send file to the browser
            $this->sendFileToBrowser($objFile);
        }
    }

    /**
     * @throws \Exception
     */
    private function setOutputBom(File $objFile, string $bomType = ''): File
    {
        $bom = '';

        if (!empty($bomType)) {
            if (!isset(ByteSequence::BOM[$bomType])) {
                throw new \Exception(sprintf('BOM type "%s" not found. BOM type has to be one of %s.', $bomType, implode(', ', array_keys(ByteSequence::BOM)), ));
            }

            $bom = ByteSequence::BOM[$bomType];
        }

        if ($bom) {
            $strContentWithBom = $bom.$objFile->getContent();
            $objFile->write($strContentWithBom);
            $objFile->close();
        }

        return $objFile;
    }
}
