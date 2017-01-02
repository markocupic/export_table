<?php
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 * @package export_table
 * @author Marko Cupic 2014
 * @link https://github.com/markocupic/export_table
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

if(Input::get('action') == 'exportTable' && Input::get('id') > 0){
       MCupic\ExportTable::prepareExport(Input::get('id'));
}


