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

// Legends
$GLOBALS['TL_LANG']['tl_export_table']['title_legend'] = 'Title settings';
$GLOBALS['TL_LANG']['tl_export_table']['settings'] = 'Settings';
$GLOBALS['TL_LANG']['tl_export_table']['deep_link_legend'] = 'Deep-Link settings';


// Fields
$GLOBALS['TL_LANG']['tl_export_table']['export_table'] = array('Export data from this table', 'Choose a table for the export.');
$GLOBALS['TL_LANG']['tl_export_table']['selected_fields'] = array('Select the fields for the export');
$GLOBALS['TL_LANG']['tl_export_table']['filterExpression'] = array('SQL "filter-expression"', 'Define filter as JSON-encoded Array -> [["published=?",1],["pid=6",1]]');
$GLOBALS['TL_LANG']['tl_export_table']['sortBy'] = array('Sort by', '');
$GLOBALS['TL_LANG']['tl_export_table']['sortByDirection'] = array('Sort by direction', '');
$GLOBALS['TL_LANG']['tl_export_table']['sortByDirection'] = array('SQL "filter-expression"', 'Define filter as JSON-encoded Array -> [["published=?",1],["pid=6",1]]');
$GLOBALS['TL_LANG']['tl_export_table']['exportType'] = array('Export type', 'Select the export type please.');
$GLOBALS['TL_LANG']['tl_export_table']['activateDeepLinkExport'] = array('Activate Deep-Link export functionality');
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkExportKey'] = array('Deep-Link key', 'Add a key to protect the download from other users.');
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfo'] = array('Link-info');
$GLOBALS['TL_LANG']['tl_export_table']['arrayDelimiter'] = array('Array Delimiter', 'Please insert an array delimiter. Normaly "||".');


// Buttons
$GLOBALS['TL_LANG']['tl_export_table']['new'][0] = 'Add new export';
$GLOBALS['TL_LANG']['tl_export_table']['new'][1] = 'Add a new export';
$GLOBALS['TL_LANG']['tl_export_table']['launchExportButton'] = 'Launch export process';

// Info text
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfoText'] = 'Use this deep link to activate the table-export in your browser:';
