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

namespace Markocupic\ExportTable\Helper;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;


class DatabaseHelper
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework){
        $this->framework = $framework;
    }

    public function listFields($strTable, $blnAssociative = false): array
    {
        $databaseAdapter = $this->framework->getAdapter(Database::class);

        if ('' === $strTable) {
            return [];
        }

        $objFields = $databaseAdapter->getInstance()
            ->listFields($strTable, 1)
        ;

        $arrFields = [];

        foreach ($objFields as $field) {
            if(!$databaseAdapter->getInstance()->fieldExists($field)){
                continue;
            }

            if (\in_array($field['name'], $arrFields, true)) {
                continue;
            }

            if ('PRIMARY' === $field['name']) {
                continue;
            }

            $arrFields[$field['name']] = $field['name'].' ['.$field['type'].']';
        }

        return $blnAssociative ? $arrFields : array_values($arrFields);

    }
}
