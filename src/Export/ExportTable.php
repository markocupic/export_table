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

namespace Markocupic\ExportTable\Export;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\System;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Helper\DatabaseHelper;
use Markocupic\ExportTable\Helper\StringHelper;
use Markocupic\ExportTable\Writer\WriterInterface;

class ExportTable
{
    private ContaoFramework $framework;
    private StringHelper $stringHelper;
    private DatabaseHelper $databaseHelper;
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
            $objConfig->setFields($arrSelectedFields);
        }

        // Use table field names as default for the header row
        if ($objConfig->getAddHeadline() && empty($objConfig->getHeadlineFields())) {
            $objConfig->setHeadlineFields($arrSelectedFields);
        }

        $strFields = empty($arrSelectedFields) ? '*' : implode(',', $arrSelectedFields);

        // The filter expression has to be entered as a JSON encoded array
        // -> [["tableName.field=? OR tableName.field=?"],["valueA","valueB"]] or
        // -> [["tableName.field=?", "tableName.field=?"],["valueA","valueB"]]
        $arrFilter = $this->generateFilterStmt($objConfig->getFilter(), $objConfig);
        $strFilterExpr = $arrFilter['stmt'];
        $arrFilterValues = $arrFilter['values'];

        // Generate the sorting expression.
        $strSortingStmt = $this->getSortingStmt($objConfig->getSortBy(), $objConfig->getSortDirection());

        $strQuery = sprintf('SELECT %s FROM %s%s%s', $strFields, $this->strTable, $strFilterExpr, $strSortingStmt);
        $objDb = $databaseAdapter
            ->getInstance()
            ->prepare($strQuery)
            ->execute(...$arrFilterValues)
        ;

        while ($arrRow = $objDb->fetchAssoc()) {
            foreach ($arrRow as $strFieldName => $varValue) {
                // HOOK: Process data with your custom hooks.
                if (isset($GLOBALS['TL_HOOKS']['exportTable']) && \is_array($GLOBALS['TL_HOOKS']['exportTable'])) {
                    foreach ($GLOBALS['TL_HOOKS']['exportTable'] as $callback) {
                        $objCallback = $systemAdapter->importStatic($callback[0]);
                        $varValue = $objCallback->{$callback[1]}($strFieldName, $varValue, $this->strTable, $arrRow, $arrDca, $objConfig);
                    }
                    $arrRow[$strFieldName] = $varValue;
                }
            }

            // Handle the row callback.
            if (null !== ($callback = $objConfig->getRowCallback())) {
                $arrRow = $callback($arrRow);
            }

            $this->arrData[] = $arrRow;
        }

        // Write export data to a file.
        $writer = $this->getWriter($objConfig->getExportType());
        $writer->write($this->arrData, $objConfig);
    }

    private function getWriter($alias): WriterInterface
    {
        return $this->writers[$alias];
    }

    /**
     * @throws \Exception
     */
    private function generateFilterStmt(array $arrFilter, Config $objConfig): array
    {
        $strFilter = json_encode($arrFilter);

        // Replace insert tags: Replace {{GET::key}} with a given value of a corresponding $_GET parameter.
        $controllerAdapter = $this->framework->getAdapter(Controller::class);
        $strFilter = $controllerAdapter->replaceInsertTags($strFilter);

        $arrFilter = json_decode($strFilter);

        // Default filter statement
        $filterStmt = '';
        $arrValues = [];

        if (!empty($arrFilter)) {
            if (2 === \count($arrFilter)) {
                // Statement
                if (\is_array($arrFilter[0])) {
                    // [["tl_member.firstname=?","tl_member.lastname=?"],["Hans","Muster"]]
                    $filterStmt .= implode(' AND ', $arrFilter[0]);
                } else {
                    // [["tl_member.firstname=? AND tl_member.lastname=?"],["Hans","Muster"]]
                    $filterStmt .= $arrFilter[0];
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

        $filterStmt = trim($filterStmt);
        $filterStmt = $filterStmt ? ' WHERE '.$filterStmt : '';

        // Check for invalid input.
        if ($this->stringHelper->testAgainstSet(strtolower($filterStmt.' ' . implode(' ', $arrValues)), $objConfig->getNotAllowedFilterExpr())) {
            $message = sprintf('Illegal filter expression! Do not use "%s" in your filter expression.', implode(', ', $objConfig->getNotAllowedFilterExpr()));

            throw new \Exception($message);
        }

        return ['stmt' => $filterStmt, 'values' => $arrValues];
    }

    private function getSortingStmt(string $strFieldName = 'id', string $direction = 'desc'): string
    {
        $arrSorting = [$strFieldName, $direction];

        return ' ORDER BY '.implode(' ', $arrSorting);
    }
}
