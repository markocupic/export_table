<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Export;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;
use Doctrine\DBAL\Connection;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Event\QueryBuilderPreparedEvent;
use Markocupic\ExportTable\Helper\DatabaseHelper;
use Markocupic\ExportTable\Helper\WhereExpressionParser;
use Markocupic\ExportTable\Writer\WriterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExportTable
{
    private array $arrData = [];

    private array $writers = [];

    public function __construct(
        private readonly Connection $connection,
        private readonly ContaoFramework $framework,
        private readonly DatabaseHelper $databaseHelper,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly WhereExpressionParser $whereExpressionParser,
    ) {
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
        $tableName = $objConfig->getTable();

        // Load the data container array.
        $this->framework->getAdapter(Controller::class)->loadDataContainer($tableName, true);

        // Load the related DCA array.
        $arrDca = $GLOBALS['TL_DCA'][$tableName] ?? [];

        // If no fields are chosen, then do list all the fields from the selected table.
        $selectedFields = $objConfig->getFields();

        if (empty($selectedFields)) {
            $selectedFields = $this->databaseHelper->listFields($tableName, false, false);
            $objConfig->setFields($selectedFields);
        }

        // Use table field names as default for the header row
        if ($objConfig->getAddHeadline() && empty($objConfig->getHeadlineFields())) {
            $objConfig->setHeadlineFields($selectedFields);
        }

        $sqlFields = empty($selectedFields) ? '*' : implode(',', $selectedFields);

        // Fetch data from the database.
        $qb = $this->connection->createQueryBuilder();
        $qb->select($sqlFields)->from($tableName);
        $qb = $this->whereExpressionParser->withWhereStmt($qb, $objConfig);

        $orderBy = $this->getOrderBy($objConfig->getSortBy(), $objConfig->getSortDirection());
        $qb->orderBy($orderBy['field'], $orderBy['direction']);

        // Dispatch the event to allow other bundles to modify the query.
        $event = new QueryBuilderPreparedEvent($qb, $objConfig);

        $this->eventDispatcher->dispatch($event);

        // Prevent further changes to the database.
        $this->connection->beginTransaction();

        try {
            $rows = $event->getQueryBuilder()->fetchAllAssociative();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            // Do not allow any further changes to the database.
            $this->connection->rollBack();
        }

        foreach ($rows as $row) {
            foreach ($row as $fieldName => $varValue) {
                // HOOK: Process data with your custom hooks.
                if (isset($GLOBALS['TL_HOOKS']['exportTable']) && \is_array($GLOBALS['TL_HOOKS']['exportTable'])) {
                    foreach ($GLOBALS['TL_HOOKS']['exportTable'] as $callback) {
                        $objCallback = $this->framework->getAdapter(System::class)->importStatic($callback[0]);
                        $varValue = $objCallback->{$callback[1]}($fieldName, $varValue, $tableName, $row, $arrDca, $objConfig);
                    }

                    $row[$fieldName] = $varValue;
                }
            }

            // Handle the row callback.
            if (null !== ($callback = $objConfig->getRowCallback())) {
                $row = $callback($row);
            }

            $this->arrData[] = $row;
        }

        // Write export data to a file.
        $writer = $this->getWriter($objConfig->getExportType());
        $writer->write($this->arrData, $objConfig);
    }

    private function getWriter($alias): WriterInterface
    {
        return $this->writers[$alias];
    }

    private function getOrderBy(string $fieldName = 'id', string $direction = 'desc'): array
    {
        return ['field' => $fieldName, 'direction' => $direction];
    }
}
