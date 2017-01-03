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
if (TL_MODE == 'BE') {
       $GLOBALS['BE_MOD']['system']['export_table'] = array(
              'icon' => 'system/modules/export_table/assets/file-export-icon-16.png',
              'tables' => array(
                     'tl_export_table'
              )
       );
}


if (TL_MODE == 'BE' && $_GET['do'] == 'export_table') {
       $GLOBALS['TL_CSS'][] = 'system/modules/export_table/assets/export_table.css';

       $GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array(
              'tl_export_table',
              'parseBackendTemplate'
       );
}

// Deep-Link support
if(Input::get('action') == 'exportTable' && Input::get('id') > 0 && Input::get('key') != ''){
       MCupic\ExportTable::prepareExport(Input::get('id'));
}


