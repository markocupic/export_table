<?php

/**
 * Export table module for Contao CMS
 * Copyright (c) 2008-2020 Marko Cupic
 * @package export_table
 * @author Marko Cupic m.cupic@gmx.ch, 2020
 * @link https://github.com/markocupic/export_table
 */

// Legends
$GLOBALS['TL_LANG']['tl_export_table']['title_legend'] = 'Title settings';
$GLOBALS['TL_LANG']['tl_export_table']['settings'] = 'Settings';
$GLOBALS['TL_LANG']['tl_export_table']['deep_link_legend'] = 'Deep-Link settings';

// Fields
$GLOBALS['TL_LANG']['tl_export_table']['export_table'] = ['Export data from this table', 'Choose a table for the export.'];
$GLOBALS['TL_LANG']['tl_export_table']['selected_fields'] = ['Select the fields for the export'];
$GLOBALS['TL_LANG']['tl_export_table']['filterExpression'] = ['SQL "filter-expression"', 'Define filter as JSON-encoded Array -> [["published=?",1],["pid=6",1]] You can use insert tags as well: -> [["published=?",1],["id=?",{{user::id}}]]'];
$GLOBALS['TL_LANG']['tl_export_table']['sortBy'] = ['Sort by', ''];
$GLOBALS['TL_LANG']['tl_export_table']['sortByDirection'] = ['Sort by direction', ''];
$GLOBALS['TL_LANG']['tl_export_table']['sortByDirection'] = ['SQL "filter-expression"', 'Define filter as JSON-encoded Array -> [["published=?",1],["pid=6",1]]'];
$GLOBALS['TL_LANG']['tl_export_table']['exportType'] = ['Export type', 'Select the export type please.'];
$GLOBALS['TL_LANG']['tl_export_table']['activateDeepLinkExport'] = ['Activate Deep-Link export functionality'];
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkExportKey'] = ['Deep-Link key', 'Add a key to protect the download from other users.'];
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfo'] = ['Link-info'];
$GLOBALS['TL_LANG']['tl_export_table']['arrayDelimiter'] = ['Array Delimiter', 'Please insert an array delimiter. Normaly "||".'];

// Buttons
$GLOBALS['TL_LANG']['tl_export_table']['new'][0] = 'Add new export';
$GLOBALS['TL_LANG']['tl_export_table']['new'][1] = 'Add a new export';
$GLOBALS['TL_LANG']['tl_export_table']['launchExportButton'] = 'Launch export process';

// Info text
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfoText'] = 'Use this deep link to activate the table-export in your browser:';
