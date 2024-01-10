<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Writer;

use Contao\File;
use Contao\FilesModel;
use League\Csv\CannotInsertRecord;
use League\Csv\InvalidArgument;
use League\Csv\Writer;
use Markocupic\ExportTable\Config\Config;

class CsvWriter extends AbstractWriter implements WriterInterface
{
    public const FILE_ENDING = 'csv';

    /**
     * @throws CannotInsertRecord
     * @throws InvalidArgument
     */
    public function write(array $arrData, Config $config): void
    {
        $filesModelAdapter = $this->framework->getAdapter(FilesModel::class);

        // Run pre-write HOOK: e.g. modify the data array
        $arrData = $this->runPreWriteHook($arrData, $config);

        if ($config->getAddHeadline() && !empty($config->getHeadlineFields())) {
            array_unshift($arrData, $config->getHeadlineFields());
        }

        // Create empty file
        $objFile = new File($this->getTargetPath($config, self::FILE_ENDING));
        $objFile->write('');
        $objFile->close();

        // Prepare the writer
        $objWriter = Writer::createFromPath($this->projectDir.'/'.$objFile->path);
        $objWriter->setDelimiter($config->getDelimiter());
        $objWriter->setEnclosure($config->getEnclosure());

        if ($config->getOutputBom()) {
            $objWriter->setOutputBom($config->getOutputBom());
        }

        // Insert records
        foreach ($arrData as $record) {
            $objWriter->insertOne(array_values($record));
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
}
