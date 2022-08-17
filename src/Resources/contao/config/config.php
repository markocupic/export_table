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

use Contao\System;
use Markocupic\ExportTable\Model\ExportTableModel;

$request = System::getContainer()->get('request_stack')->getCurrentRequest();

/*
 * Back end modules
 */
if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequst($request)) {
    $GLOBALS['BE_MOD']['system']['export_table'] = [
        'tables' => [
            'tl_export_table',
        ],
    ];
}

if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequst($request) && 'export_table' === $request->query->get('do')) {
    $GLOBALS['TL_CSS'][] = 'bundles/markocupicexporttable/export_table.css';
}

// Register contao models
$GLOBALS['TL_MODELS']['tl_export_table'] = ExportTableModel::class;

// ****** exportTable Hook *********
// With the exportTable Hook you can control the output
// Please ensure that the hook container will be loaded before the export_table container.
// $GLOBALS['TL_HOOKS']['exportTable'][] = array('\MyNamespace\MyPackage\MyClass','myMethod');
