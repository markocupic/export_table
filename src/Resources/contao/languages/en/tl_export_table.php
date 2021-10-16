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

// Global operations
$GLOBALS['TL_LANG']['tl_export_table']['new'] = ['Add new export.', 'Add new export.'];

// Operations
$GLOBALS['TL_LANG']['tl_export_table']['export'] = ['Run export with ID %s.', 'Run export with ID %s.'];

// Legends
$GLOBALS['TL_LANG']['tl_export_table']['title_legend'] = 'Title settings';
$GLOBALS['TL_LANG']['tl_export_table']['settings'] = 'Settings';
$GLOBALS['TL_LANG']['tl_export_table']['deep_link_legend'] = 'Deep-Link settings';
$GLOBALS['TL_LANG']['tl_export_table']['save_legend'] = "Save destination";

// Fields
$GLOBALS['TL_LANG']['tl_export_table']['title'] = ['Title', 'Enter the title please.'];
$GLOBALS['TL_LANG']['tl_export_table']['table'] = ['Table', 'Select a data table for the export please.'];
$GLOBALS['TL_LANG']['tl_export_table']['fields'] = ['Fields', 'Select the fields for the export please.'];
$GLOBALS['TL_LANG']['tl_export_table']['addHeadline'] = ['Add headline', 'Indicate whether the header with the field names should be added to the export.'];
$GLOBALS['TL_LANG']['tl_export_table']['exportType'] = ['Export type', 'Select the export type please.'];
$GLOBALS['TL_LANG']['tl_export_table']['filter'] = ['SQL "filter-expression"', 'Define a filter as JSON-encoded Array -> [["tl_calendar_events.published=? AND tl_calendar_events.pid=?"],["1",6]] You can add insert tags as well: -> [["tl_member.id=?"],[{{user::id}}]]'];
$GLOBALS['TL_LANG']['tl_export_table']['sortBy'] = ['Sort by', 'Add a sort by field please.'];
$GLOBALS['TL_LANG']['tl_export_table']['sortDirection'] = ['Sort by direction', 'Select sorting direction please.'];
$GLOBALS['TL_LANG']['tl_export_table']['enclosure'] = ['Enclosure', 'Please enter the enclosure tag (normally \'"\').'];
$GLOBALS['TL_LANG']['tl_export_table']['delimiter'] = ['Delimiter', 'Please enter the delimiter tag (normally ";").'];
$GLOBALS['TL_LANG']['tl_export_table']['arrayDelimiter'] = ['Array Delimiter', 'Please insert an array delimiter. Normally "||".'];
$GLOBALS['TL_LANG']['tl_export_table']['sendFileToTheBrowser'] = ['Download the file in the browser', 'Indicate whether or not to download the file in the browser.'];
$GLOBALS['TL_LANG']['tl_export_table']['activateDeepLinkExport'] = ['Activate Deep-Link export functionality'];
$GLOBALS['TL_LANG']['tl_export_table']['token'] = ['Deep-Link key', 'Add a key to protect the download from other users.'];
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfo'] = ['Link-info'];
$GLOBALS['TL_LANG']['tl_export_table']['saveExport'] = ['Save export to the Contao filesystem'];
$GLOBALS['TL_LANG']['tl_export_table']['overrideFile'] = ['Override file with same filename', 'Please select whether files with the same name should be overwritten.'];
$GLOBALS['TL_LANG']['tl_export_table']['saveExportDirectory'] = ['Export directory', 'Please choose the save destination.'];
$GLOBALS['TL_LANG']['tl_export_table']['filename'] = ['File name', 'Select a file name for the export please. If the field is left empty, the table name is selected for the file name.'];

// Reference
$GLOBALS['TL_LANG']['tl_export_table']['csv'] = 'CSV default export';
$GLOBALS['TL_LANG']['tl_export_table']['xml'] = 'XML default export';

// Info text
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfoText'] = 'Use this deep link to activate the table-export in your browser:';
