<?php

/**
 * Export table module for Contao CMS
 * Copyright (c) 2008-2020 Marko Cupic
 * @package export_table
 * @author Marko Cupic m.cupic@gmx.ch, 2020
 * @link https://github.com/markocupic/export_table
 */

$GLOBALS['TL_LANG']['tl_export_table']['title_legend'] = "Titel-Einstellungen";
$GLOBALS['TL_LANG']['tl_export_table']['settings'] = "Einstellungen";
$GLOBALS['TL_LANG']['tl_export_table']['deep_link_legend'] = "Deep-Link Einstellungen";
$GLOBALS['TL_LANG']['tl_export_table']['title'][0] = "Namen";
$GLOBALS['TL_LANG']['tl_export_table']['title'][1] = "Geben Sie einen Namen ein.";
$GLOBALS['TL_LANG']['tl_export_table']['export_table'][0] = "Datentabelle für Export auswählen";
$GLOBALS['TL_LANG']['tl_export_table']['export_table'][1] = "Wählen Sie eine Tabelle für den Exportvorgang aus.";
$GLOBALS['TL_LANG']['tl_export_table']['fields'][0] = "Felder für Exportvorgang auswählen.";
$GLOBALS['TL_LANG']['tl_export_table']['new'][0] = "Neuen Exportdatensatz anlegen";
$GLOBALS['TL_LANG']['tl_export_table']['new'][1] = "Einen neuen Exportdatensatz anlegen";
$GLOBALS['TL_LANG']['tl_export_table']['launchExportButton'] = "Exportvorgang starten";
$GLOBALS['TL_LANG']['tl_export_table']['exportType'][0] = 'Export-Typ';
$GLOBALS['TL_LANG']['tl_export_table']['exportType'][1] = 'Bitte wählen Sie einen Export-Typ aus.';
$GLOBALS['TL_LANG']['tl_export_table']['filterExpression'][0] = 'SQL-Filter';
$GLOBALS['TL_LANG']['tl_export_table']['filterExpression'][1] = 'Definieren Sie einen Filter in der Form eines JSON-kodierten Arrays -> [["published=?",1],["pid=6",1]] Auch Insert Tags sind möglich: -> [["published=?",1],["id=?",{{user::id}}]]';
$GLOBALS['TL_LANG']['tl_export_table']['sortBy'][0] = 'Sortierung';
$GLOBALS['TL_LANG']['tl_export_table']['sortByDirection'][0] = 'Sortierrichtung';
$GLOBALS['TL_LANG']['tl_export_table']['activateDeepLinkExport'][0] = 'Deep-Link Export-Funktion aktivieren';
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkExportKey'][0] = 'Deep-Link Schl&uuml;ssel';
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkExportKey'][1] = 'Geben Sie einen Schl&uuml;ssel ein, um den Download zu sch&uuml;tzen.';
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfo'][0] = 'Link-Info';
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfoText'] = 'Benutzen Sie diesen Link, um die Tabellen-Exportfunktion in Ihrem Browser zu nutzen:';
$GLOBALS['TL_LANG']['tl_export_table']['arrayDelimiter'] = ['Array Trennzeichen', 'Geben Sie ein Trennzeichen ein, mit dem Arrays getrennt werden. Im Normalfall "||".'];
