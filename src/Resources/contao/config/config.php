<?php

/*
 * This file is part of Export Table for Contao CMS.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

use Contao\Input;
use Markocupic\ExportTable\Model\ExportTableModel;

/**
 * Back end modules
 */
if (TL_MODE === 'BE')
{
	$GLOBALS['BE_MOD']['system']['export_table'] = array(
		'tables' => array(
			'tl_export_table',
		),
	);
}

if (TL_MODE === 'BE' && Input::get('do') === 'export_table')
{
	$GLOBALS['TL_CSS'][] = 'bundles/markocupicexporttable/export_table.css';
}

// Register contao models
$GLOBALS['TL_MODELS']['tl_export_table'] = ExportTableModel::class;

// ****** exportTable Hook *********
// With the exportTable Hook you can control the output
// Please ensure that the hook container will be loaded before the export_table container.
// $GLOBALS['TL_HOOKS']['exportTable'][] = array('\MyNamespace\MyPackage\MyClass','myMethod');
