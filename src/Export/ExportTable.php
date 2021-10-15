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

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\System;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Helper\DatabaseHelper;
use Markocupic\ExportTable\Helper\StringHelper;
use Markocupic\ExportTable\Writer\WriterInterface;

/**
 * Class ExportTable.
 */
class ExportTable
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var Str
     */
    private $stringHelper;

    /**
     * @var DatabaseHelper
     */
    private $databaseHelper;

    /**
     * @var string
     */
    private $strTable;

    /**
     * @var array
     */
    private $arrData = [];

    /**
     * @var array
     */
    private $writers = [];

    public function __construct(ContaoFramework $framework, StringHelper $stringHelper, DatabaseHelper $databaseHelper)
    {
        $this->framework = $framework;
        $this->stringHelper = $stringHelper;
        $this->databaseHelper = $databaseHelper;
    }

    /**
     * Add a writer service for a given alias.
     * @param WriterInterface $resource
     * @param string $alias
     */
    public function addWriter(WriterInterface $resource, string $alias): void
    {
        $this->writers[$alias] = $resource;
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
            $arrSelectedFields = $this->databaseHelper->listFields($this->strTable, false, false);
        }

        $strFields = empty($arrSelectedFields) ? '*' : implode(',', $arrSelectedFields);

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
            ->prepare('SELECT '.$strFields.' FROM  '.$this->strTable.' WHERE '.$arrFilterStmt['stmt'].' ORDER BY '.$strSortingStmt)
            ->execute(...$arrFilterStmt['values'])
        ;

        while ($arrRow = $objDb->fetchAssoc()) {
            foreach ($arrSelectedFields as $strFieldname) {
                // HOOK: Process data with your custom hooks.
                if (isset($GLOBALS['TL_HOOKS']['exportTable']) && \is_array($GLOBALS['TL_HOOKS']['exportTable'])) {
                    foreach ($GLOBALS['TL_HOOKS']['exportTable'] as $callback) {
                        $objCallback = $systemAdapter->importStatic($callback[0]);
                        $arrRow[$strFieldname] = $objCallback->{$callback[1]}($strFieldname, $arrRow[$strFieldname], $this->strTable, $arrRow, $arrDca, $objConfig);
                    }
                }
            }

            // Handle row callback.
            if (null !== ($callback = $objConfig->getRowCallback())) {
                $arrRow = $callback($arrRow);
            }

            $this->arrData[] = array_values($arrRow);
        }

        // Write export data to a file.
        $writer = $this->getWriter($objConfig->getModel()->exportType);
        $writer->write($this->arrData, $objConfig);
    }

    private function getWriter($alias)
    {
        return $this->writers[$alias];
    }

    /**
     * @throws \Exception
     */
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
        if ($this->stringHelper->testAgainstSet(strtolower($filterStmt.' '.$arrValues), $objConfig->getNotAllowedFilterExpr())) {
            $message = sprintf('Illegal filter expression! Do not use "%s" in your filter expression.', implode(', ', $objConfig->getNotAllowedFilterExpr()));

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
