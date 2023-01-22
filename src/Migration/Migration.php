<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class Migration extends AbstractMigration
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function shouldRun(): bool
    {
        $doMigration = false;

        $schemaManager = $this->connection->createSchemaManager();

        // If the database table itself does not exist we should do nothing
        if ($schemaManager->tablesExist(['tl_export_table'])) {
            $columns = $schemaManager->listTableColumns('tl_export_table');

            // # Rename tl_export_table.export_table -> tl_export_table.exportTable
            if (isset($columns['export_table']) && !isset($columns['exporttable'])) {
                $doMigration = true;
            }

            // # Rename tl_export_table.table -> tl_export_table.exportTable
            if (isset($columns['`table`']) && !isset($columns['exporttable'])) {
                $doMigration = true;
            }

            // # Rename tl_export_table.filterExpression-> tl_export_table.filter
            if (isset($columns['filterexpression']) && !isset($columns['filter'])) {
                $doMigration = true;
            }

            // # Rename tl_export_table.deepLinkExportkey-> tl_export_table.token
            if (isset($columns['deeplinkexportkey']) && !isset($columns['token'])) {
                $doMigration = true;
            }

            // # Rename tl_export_table.sortByDirection -> tl_export_table.sortdirection
            if (isset($columns['sortbydirection']) && !isset($columns['sortdirection'])) {
                $doMigration = true;
            }
        }

        return $doMigration;
    }

    /**
     * @throws Exception
     */
    public function run(): MigrationResult
    {
        $arrMessage = [];

        $schemaManager = $this->connection->createSchemaManager();

        // Rename fields
        if ($schemaManager->tablesExist(['tl_export_table'])) {
            $columns = $schemaManager->listTableColumns('tl_export_table');

            if (isset($columns['export_table']) && !isset($columns['exporttable'])) {
                $this->connection->executeQuery('ALTER TABLE tl_export_table CHANGE `export_table` `exportTable` varchar(255)');
                $arrMessage[] = 'Rename field tl_export_table.export_table to tl_export_table.exportTable.';
            }

            if (isset($columns['`table`']) && !isset($columns['exporttable'])) {
                $this->connection->executeQuery('ALTER TABLE tl_export_table CHANGE `table` `exportTable` varchar(255)');
                $arrMessage[] = 'Rename field tl_export_table.table to tl_export_table.exportTable.';
            }

            if (isset($columns['filterexpression']) && !isset($columns['filter'])) {
                $this->connection->executeQuery('ALTER TABLE tl_export_table CHANGE `filterexpression` `filter` varchar(255)');
                $arrMessage[] = 'Rename field tl_export_table.filterExpression to tl_export_table.filter.';
            }

            if (isset($columns['deeplinkexportkey']) && !isset($columns['token'])) {
                $this->connection->executeQuery('ALTER TABLE tl_export_table CHANGE `deeplinkexportkey` `token` varchar(255)');
                $arrMessage[] = 'Rename field tl_export_table.deepLinkExportKey to tl_export_table.token.';
            }

            if (isset($columns['sortbydirection']) && !isset($columns['sortdirection'])) {
                $this->connection->executeQuery('ALTER TABLE tl_export_table CHANGE `sortByDirection` `sortDirection` varchar(64)');
                $arrMessage[] = 'Rename field tl_export_table.sortByDirection to tl_export_table.sortDirection.';
            }
        }

        return new MigrationResult(
            true,
            implode(' ', $arrMessage)
        );
    }
}
