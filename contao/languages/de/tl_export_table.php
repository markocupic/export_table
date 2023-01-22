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

// Global operations
$GLOBALS['TL_LANG']['tl_export_table']['new'] = ['Neuen Exportdatensatz anlegen', 'Einen neuen Exportdatensatz anlegen'];

// Operations
$GLOBALS['TL_LANG']['tl_export_table']['export'] = ['Export mit ID %s starten.', 'Export mit ID %s starten.'];

// Legends
$GLOBALS['TL_LANG']['tl_export_table']['title_legend'] = 'Titel-Einstellungen';
$GLOBALS['TL_LANG']['tl_export_table']['settings'] = 'Einstellungen';
$GLOBALS['TL_LANG']['tl_export_table']['deep_link_legend'] = 'Deep-Link-Einstellungen';
$GLOBALS['TL_LANG']['tl_export_table']['save_legend'] = 'Speicher-Einstellungen';

// Fields
$GLOBALS['TL_LANG']['tl_export_table']['title'] = ['Titel', 'Geben Sie einen Namen ein.'];
$GLOBALS['TL_LANG']['tl_export_table']['table'] = ['Datentabelle', 'Wählen Sie eine Tabelle für den Exportvorgang aus.'];
$GLOBALS['TL_LANG']['tl_export_table']['fields'] = ['Felder', 'Wählen Sie die Felder für den Export aus.'];
$GLOBALS['TL_LANG']['tl_export_table']['addHeadline'] = ['Kopfzeile hinzufügen', 'Geben Sie an, ob die Kopfzeile mit den Feldnamen dem Export hinzugefügt werden soll.'];
$GLOBALS['TL_LANG']['tl_export_table']['exportType'] = ['Export-Typ', 'Bitte wählen Sie einen Export-Typ aus.'];
$GLOBALS['TL_LANG']['tl_export_table']['filter'] = ['SQL-Filter', 'Definieren Sie einen Filter in der Form eines JSON-kodierten Arrays -> [["tl_calendar_events.published=? AND tl_calendar_events.pid=?"],["1",6]] Auch Insert Tags sind möglich: -> [["tl_member.id=?"],[{{user::id}}]]'];
$GLOBALS['TL_LANG']['tl_export_table']['sortBy'] = ['Sortierung', 'Geben Sie das Feld an, nachdem sortiert werden soll.'];
$GLOBALS['TL_LANG']['tl_export_table']['sortDirection'] = ['Sortierrichtung', 'Geben Sie die Sortierrichtung an.'];
$GLOBALS['TL_LANG']['tl_export_table']['enclosure'] = ['Enclosure', 'Geben Sie die Enclosure an (im Normalfall \'"\').'];
$GLOBALS['TL_LANG']['tl_export_table']['delimiter'] = ['Delimiter', 'Geben Sie den Delimitter an (im Normalfall ";").'];
$GLOBALS['TL_LANG']['tl_export_table']['bom'] = ['BOM hinzufügen', 'Füge der Datei das BOM (Byte Order Mark) hinzu (korrekte Darstellung von UTF-codierten Zeichen in MS-Excel).'];
$GLOBALS['TL_LANG']['tl_export_table']['arrayDelimiter'] = ['Array Trennzeichen', 'Geben Sie ein Trennzeichen ein, mit dem Arrays getrennt werden. Im Normalfall "||".'];
$GLOBALS['TL_LANG']['tl_export_table']['sendFileToTheBrowser'] = ['Datei im Browser herunterladen', 'Geben Sie ein an, ob die Datei im Browser heruntergeladen werden soll oder nicht.'];
$GLOBALS['TL_LANG']['tl_export_table']['activateDeepLinkExport'] = ['Deep-Link Export aktivieren.', 'Deep-Link Export-Funktion aktivieren.'];
$GLOBALS['TL_LANG']['tl_export_table']['token'] = ['Deep-Link Schlüssel', 'Geben Sie einen Schlüssel ein, um den Download zu schützen.'];
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfo'] = ['Link-Info'];
$GLOBALS['TL_LANG']['tl_export_table']['saveExport'] = ['Export im Contao Dateisystem abspeichern'];
$GLOBALS['TL_LANG']['tl_export_table']['overrideFile'] = ['Gleichnamige Datei überschreiben', 'Bitte wählen Sie aus, ob gleichnamige Dateien überschrieben werden sollen.'];
$GLOBALS['TL_LANG']['tl_export_table']['saveExportDirectory'] = ['Export-Verzeichnis', 'Bitte wählen Sie ein Export-Verzeichnis aus.'];
$GLOBALS['TL_LANG']['tl_export_table']['filename'] = ['Dateiname (ohne Dateiendung)', 'Bitte wählen Sie für den Export einen Dateinamen aus. Wird das Feld leer gelassen, wird für den Dateinamen der Tabellenname gewählt.'];

// Reference
$GLOBALS['TL_LANG']['tl_export_table']['csv'] = 'CSV Standard Export';
$GLOBALS['TL_LANG']['tl_export_table']['xml'] = 'XML Standard Export';

// Info text
$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfoText'] = 'Benutzen Sie diesen Link, um die Tabellen-Exportfunktion in Ihrem Browser zu nutzen:';
