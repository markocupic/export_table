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

namespace Markocupic\ExportTable\Export;

use Contao\Backend;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\System;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Helper\Str;
use Markocupic\ExportTable\Writer\CsvWriter;
use Markocupic\ExportTable\Writer\XmlWriter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ExportTable.
 */
class ExportTable extends Backend
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
     * @var CsvWriter
     */
    private $csvWriter;

    /**
     * @var XmlWriter
     */
    private $xmlWriter;

    /**
     * @var Str
     */
    private $strHelper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $strTable;

    /**
     * @var array
     */
    private $arrData = [];

    public function __construct(ContaoFramework $framework, RequestStack $requestStack, CsvWriter $csvWriter, XmlWriter $xmlWriter, Str $strHelper, TranslatorInterface $translator, string $projectDir)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
        $this->csvWriter = $csvWriter;
        $this->xmlWriter = $xmlWriter;
        $this->strHelper = $strHelper;
        $this->translator = $translator;
        $this->projectDir = $projectDir;
    }

    /**
     * @throws \Exception
     */
    public function run(Config $objConfig): void
    {
        $this->strTable = $objConfig->getTable();

        $databaseAdapter = $this->framework->getAdapter(Database::class);
        $controllerAdapter = $this->framework->getAdapter(Controller::class);
        $systemAdapter = $this->framework->getAdapter(System::class);

        // Load the data container array.
        $controllerAdapter->loadDataContainer($this->strTable, true);
        $arrDca = $GLOBALS['TL_DCA'][$this->strTable] ?? [];

        // If no fields are chosen, then do list all the fields from the selected table.
        $arrSelectedFields = $objConfig->getFields();

        if (empty($arrSelectedFields)) {
            $arrSelectedFields = $databaseAdapter->getInstance()->getFieldNames($this->strTable);
        }

        // Load the language files for the headline fields.
        if (!empty($objConfig->getHeadlineLabelLang())) {
            $controllerAdapter->loadLanguageFile($this->strTable, $objConfig->getHeadlineLabelLang());
        }

        $arrHeadline = [];

        foreach ($arrSelectedFields as $strFieldname) {
            $arrHeadline[] = $arrDca[$strFieldname][0] ?? $strFieldname;
        }

        // First add the headline to the data array.
        $this->arrData[] = $arrHeadline;

        // Generate filter expression.
        // Enter the filter expression as a JSON encoded array -> [["tablename.field=? OR tablename.field=?"],["valueA","valueB"]].
        $arrFilterStmt = $this->generateFilterStmt($objConfig->getFilter(), $objConfig);

        // Generate the sorting expression.
        $strSortingStmt = $this->getSortingStmt($objConfig->getSortBy(), $objConfig->getSortDirection());

        $objDb = $databaseAdapter->getInstance()
            ->prepare('SELECT * FROM  '.$this->strTable.' WHERE '.$arrFilterStmt['stmt'].' ORDER BY '.$strSortingStmt)
            ->execute(...$arrFilterStmt['values'])
        ;

        while ($arrDataRecord = $objDb->fetchAssoc()) {
            $arrRow = [];

            foreach ($arrSelectedFields as $strFieldname) {
                $varValue = $arrDataRecord[$strFieldname];

                // HOOK: Process data with your custom hooks.
                if (isset($GLOBALS['TL_HOOKS']['exportTable']) && \is_array($GLOBALS['TL_HOOKS']['exportTable'])) {
                    foreach ($GLOBALS['TL_HOOKS']['exportTable'] as $callback) {
                        $objCallback = $systemAdapter->importStatic($callback[0]);
                        $varValue = $objCallback->{$callback[1]}($strFieldname, $varValue, $this->strTable, $arrDataRecord, $arrDca, $objConfig);
                    }
                }

                $arrRow[] = $varValue;
            }
            $this->arrData[] = $arrRow;
        }

        // XML
        if ('xml' === $objConfig->getExportType()) {
            $this->xmlWriter->write($this->arrData, $objConfig);
        }

        // CSV
        elseif ('csv' === $objConfig->getExportType()) {
            $this->csvWriter->write($this->arrData, $objConfig);
        }
    }

    private function generateFilterStmt(array $arrFilter, Config $objConfig): array
    {

        $strFilter = json_encode($arrFilter);

        // Replace insert tags: Replace {{GET::key}} with a given value of certain $_GET parameter.
        $controllerAdapter = $this->framework->getAdapter(Controller::class);
        $strFilter = $controllerAdapter->replaceInsertTags($strFilter);

        $arrFilter = json_decode($strFilter);

        // Default filter statement
        $filterStmt = $this->strTable.'.id>?';
        $arrValues = [0];

        if (!empty($arrFilter)) {
            if (2 === \count($arrFilter)) {
                // Statement
                if (\is_array($arrFilter[0])) {
                    $filterStmt .= ' AND '.implode(' AND ', $arrFilter[0]);
                } else {
                    $filterStmt .= ' AND '.$arrFilter[0];
                }

                // Values
                if (\is_array($arrFilter[1])) {
                    foreach ($arrFilter[1] as $v) {
                        $arrValues[] = $v;
                    }
                } else {
                    $arrValues[] = $arrFilter[1];
                }
            }
        }

        // Check for invalid input.
        if ($this->strHelper->testAgainstSet(strtolower($filterStmt.' '.$arrValues), $objConfig->getNotAllowedFilterExpr())) {
            $message = $this->translator->trans('XPT.exportTblNotAllowedFilterExpression', [implode(', ', $objConfig->getNotAllowedFilterExpr())], 'contao_default');

            throw new \Exception($message);
        }

        return ['stmt' => $filterStmt, 'values' => $arrValues];
    }

    private function getSortingStmt(string $strFieldname = 'id', string $direction = 'desc'): string
    {
        $arrSorting = [$strFieldname, $direction];

        return implode(' ', $arrSorting);
    }
}
