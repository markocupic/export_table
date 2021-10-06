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

namespace Markocupic\ExportTable\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class Migration extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function shouldRun(): bool
    {
        $doMigration = false;

        $schemaManager = $this->connection->getSchemaManager();

        // If the database table itself does not exist we should do nothing
        if ($schemaManager->tablesExist(['tl_export_table'])) {
            $columns = $schemaManager->listTableColumns('tl_export_table');

            // # Rename tl_export_table.export_table -> tl_export_table.exporttable
            if (isset($columns['export_table']) && !isset($columns['exporttable'])) {
                $doMigration = true;
            }

            // # Rename tl_export_table.sortbydirection -> tl_export_table.sortdirection
            if (isset($columns['sortbydirection']) && !isset($columns['sortdirection'])) {
                $doMigration = true;
            }
        }

        return $doMigration;
    }

    public function run(): MigrationResult
    {
        $arrMessage = [];

        $schemaManager = $this->connection->getSchemaManager();

        // #4 Rename tl_module.calendar_event_booking_member_list_partial_template to tl_module.calendarEventBookingMemberListPartialTemplate
        if ($schemaManager->tablesExist(['tl_export_table'])) {
            $columns = $schemaManager->listTableColumns('tl_export_table');

            if (isset($columns['export_table']) && !isset($columns['exporttable'])) {
                $this->connection->query('ALTER TABLE tl_export_table CHANGE export_table exportTable varchar(255)');
                $arrMessage[] = 'Rename field tl_export_table.export_table to tl_export_table.exportTable.';
            }

            if (isset($columns['sortbydirection']) && !isset($columns['sortdirection'])) {
                $this->connection->query('ALTER TABLE tl_export_table CHANGE sortByDirection sortDirection varchar(64)');
                $arrMessage[] = 'Rename field tl_export_table.sortByDirection to tl_export_table.sortDirection.';
            }
        }

        return new MigrationResult(
            true,
            implode(' ', $arrMessage)
        );
    }
}
