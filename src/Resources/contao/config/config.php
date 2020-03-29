<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package export_table
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Back end modules
 */
if (TL_MODE === 'BE')
{
    $GLOBALS['BE_MOD']['system']['export_table'] = array(
        'icon'   => 'bundles/markocupicexporttable/file-export-icon-16.png',
        'tables' => array(
            'tl_export_table',
        ),
    );
}


if (TL_MODE === 'BE' && $_GET['do'] === 'export_table')
{
    $GLOBALS['TL_CSS'][] = 'bundles/markocupicexporttable/export_table.css';
}

// ****** exportTable Hook *********
// With the exportTable Hook you can control the output
// Please ensure that the hook container will be loaded before the export_table container.
// In Contao 4 you have to load the hook container via the AppKernel.php right before the export_table container
// $GLOBALS['TL_HOOKS']['exportTable'][] = array('\MyNamespace\MyPackage\MyClass','myMethod');

// Deep-Link support
if (TL_MODE === 'FE' && Input::get('action') === 'exportTable' && Input::get('key') != '')
{
    // In Contao 4 initializeSystem won't work
    $GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('\Markocupic\ExportTable\ExportTable', 'prepareExport');
}


