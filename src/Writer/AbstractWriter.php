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

use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\File;
use Contao\FilesModel;
use Contao\Message;
use Contao\System;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Logger\Logger;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractWriter
{
    protected ContaoFramework $framework;
    protected TranslatorInterface $translator;
    protected Logger $logger;
    protected string $projectDir;

    public function __construct(ContaoFramework $framework, TranslatorInterface $translator, Logger $logger, string $projectDir)
    {
        $this->framework = $framework;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->projectDir = $projectDir;
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

    protected function sendFileToBrowser(File $objFile, bool $blnInline = false): void
    {
        $filePath = $this->projectDir.'/'.$objFile->path;

        $response = new BinaryFileResponse($filePath);
        $response->setPrivate(); // public by default
        $response->setAutoEtag();

        $response->setContentDisposition(
            $blnInline ? ResponseHeaderBag::DISPOSITION_INLINE : ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $objFile->basename,
            (new UnicodeString(basename($filePath)))->ascii()->toString()
        );

        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType($filePath);

        $response->headers->addCacheControlDirective('must-revalidate');
        $response->headers->set('Connection', 'close');
        $response->headers->set('Content-Type', $mimeType);

        throw new ResponseException($response);
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

    protected function addBom(File $objFile, string $bomType = ''): File
    {
        switch ($bomType) {
            case 'UTF-8':
                $bom = $this->getUtf8Bom();
                break;

            default:
                $bom = '';
        }

        if ($bom) {
            $strContentWithBom = $bom.$objFile->getContent();
            $objFile->write($strContentWithBom);
            $objFile->close();
        }

        return $objFile;
    }

    private function getUtf8Bom(): string
    {
        return \chr(hexdec('EF')).\chr(hexdec('BB')).\chr(hexdec('BF'));
    }
}
