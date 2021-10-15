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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\File;
use Contao\FilesModel;
use Contao\Message;
use Contao\System;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Logger\Logger;
use Psr\Log\LogLevel;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractWriter
{
    /**
     * @var ContaoFramework
     */
    protected $framework;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(ContaoFramework $framework, TranslatorInterface $translator, Logger $logger)
    {
        $this->framework = $framework;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    protected function getTargetPath(Config $objConfig, string $strFileEnding): string
    {
        $filesModelAdapter = $this->framework->getAdapter(FilesModel::class);

        $fn = $objConfig->getFilename();

        if (!\strlen((string) $fn)) {
            $fn = $objConfig->getTable();
        }

        $appendDateString = $objConfig->getOverrideFile() ? '' : date('_Ymd_His', time());

        $filename = sprintf('%s%s.%s', $fn, $appendDateString, $strFileEnding);

        if ($objConfig->getSaveExport() && $objConfig->getSaveExportDirectory() && null !== ($filesModel = $filesModelAdapter->findByUuid($objConfig->getSaveExportDirectory()))) {
            $objFile = new File($filesModel->path.'/'.$filename);

            return $objFile->path;
        }
        $objFile = new File($objConfig->getTempFolder().'/'.$filename);

        return $objFile->path;
    }

    protected function sendFileToTheBrowser(File $objFile, bool $blnInline = false): void
    {
        $objFile->sendToBrowser($objFile->basename, $blnInline);
    }

    protected function runPreWriteHook(array $arrData, Config $objConfig): array
    {
        $systemAdapter = $this->framework->getAdapter(System::class);

        // HOOK: Insert your custom code.
        if (isset($GLOBALS['TL_HOOKS']['exportTablePreWrite']) && \is_array($GLOBALS['TL_HOOKS']['exportTablePreWrite'])) {
            foreach ($GLOBALS['TL_HOOKS']['exportTablePreWrite'] as $callback) {
                $objCallback = $systemAdapter->importStatic($callback[0]);
                $arrData = $objCallback->{$callback[1]}($arrData, $objConfig);
            }
        }

        return $arrData;
    }

    protected function runPostWriteHook(File $objFile, Config $objConfig): File
    {
        $systemAdapter = $this->framework->getAdapter(System::class);

        // HOOK: Insert your custom code.
        if (isset($GLOBALS['TL_HOOKS']['exportTablePostWrite']) && \is_array($GLOBALS['TL_HOOKS']['exportTablePostWrite'])) {
            foreach ($GLOBALS['TL_HOOKS']['exportTablePostWrite'] as $callback) {
                $objCallback = $systemAdapter->importStatic($callback[0]);
                $objFile = $objCallback->{$callback[1]}($objFile, $objConfig);
            }
        }




        return $objFile;
    }

    protected function sendBackendMessage(File $objFile): void
    {
        $messageAdapter = $this->framework->getAdapter(Message::class);
        $msg = $this->translator->trans('MSC.savedExportFile', [$objFile->path], 'contao_default');
        $messageAdapter->addInfo($msg);
    }

    protected function systemLog(File $objFile): void
    {
        $messageAdapter = $this->framework->getAdapter(Message::class);
        $msg = $this->translator->trans('MSC.savedExportFile', [$objFile->path], 'contao_default');
        $messageAdapter->addInfo($msg);
    }

    protected function log(File $objFile, Config $objConfig): void
    {
        $strText = sprintf('Run ExportTable for "%s" and stored file to "%s".', $objConfig->getTable(), $objFile->path);
        $this->logger->log($strText, LogLevel::INFO, ContaoContext::GENERAL, __METHOD__);
    }
}
