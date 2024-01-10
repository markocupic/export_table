<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

use Markocupic\ExportTable\Model\ExportTableModel;

/*
 * Back end modules
 */
$GLOBALS['BE_MOD']['system']['export_table'] = [
    'tables'     => [
        'tl_export_table',
    ],
    'stylesheet' => ['bundles/markocupicexporttable/export_table.css'],

];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_export_table'] = ExportTableModel::class;

// ****** exportTable Hook *********
// With the exportTable Hook you can control the output
// Please ensure that the hook container will be loaded before the export_table container.
// $GLOBALS['TL_HOOKS']['exportTable'][] = array('\MyNamespace\MyPackage\MyClass','myMethod');
