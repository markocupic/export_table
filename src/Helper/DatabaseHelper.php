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

namespace Markocupic\ExportTable\Helper;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;

class DatabaseHelper
{
    private ContaoFramework $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function listFields(string $strTable = '', $blnAssociative = false, $blnAddType = false): array
    {
        $databaseAdapter = $this->framework->getAdapter(Database::class);

        $arrFields = [];

        if (!$strTable || !$databaseAdapter->getInstance()->tableExists($strTable)) {
            return $arrFields;
        }

        $objFields = $databaseAdapter->getInstance()->listFields($strTable, true);

        foreach ($objFields as $arrField) {
            if (!$databaseAdapter->getInstance()->fieldExists($arrField['name'], $strTable)) {
                continue;
            }

            if (\in_array($arrField['name'], $arrFields, true)) {
                continue;
            }

            if ('index' === $arrField['type']) {
                continue;
            }

            if ('PRIMARY' === $arrField['name']) {
                continue;
            }

            if ($blnAddType) {
                $arrFields[$arrField['name']] = $arrField['name'].' - '.$arrField['origtype'];
            } else {
                $arrFields[$arrField['name']] = $arrField['name'];
            }
        }

        return $blnAssociative ? $arrFields : array_values($arrFields);
    }
}
