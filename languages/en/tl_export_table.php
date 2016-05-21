<?php
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 * @package export_table
 * @author Marko Cupic 2014
 * @link https://github.com/markocupic/export_table
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

// legends
$GLOBALS['TL_LANG']['tl_export_table']['settings'] = 'Settings';

// fields
$GLOBALS['TL_LANG']['tl_export_table']['export_table'] = array('Export data from this table', 'Choose a table for the export.');
$GLOBALS['TL_LANG']['tl_export_table']['selected_fields'] = array('Select the fields for the export');
$GLOBALS['TL_LANG']['tl_export_table']['filterExpression'] = array('SQL "filter-expression"', 'Define filter as JSON-encoded Array -> [["published=?",1],["pid=6",1]]');
$GLOBALS['TL_LANG']['tl_export_table']['sortBy'] = array('Sort by', '');
$GLOBALS['TL_LANG']['tl_export_table']['sortByDirection'] = array('Sort by direction', '');
$GLOBALS['TL_LANG']['tl_export_table']['sortByDirection'] = array('SQL "filter-expression"', 'Define filter as JSON-encoded Array -> [["published=?",1],["pid=6",1]]');
$GLOBALS['TL_LANG']['tl_export_table']['exportType'] = array('Export type', 'Select the export type please.');
$GLOBALS['TL_LANG']['tl_export_table']['destinationCharset'] = array('Destination charset', 'Select the destination charset. Default to "UTF-8" or "Windows-1252".');




//buttons
$GLOBALS['TL_LANG']['tl_export_table']['new'][0] = 'Add new export';
$GLOBALS['TL_LANG']['tl_export_table']['new'][1] = 'Add a new export';
$GLOBALS['TL_LANG']['tl_export_table']['launchExportButton'] = 'Launch export process';