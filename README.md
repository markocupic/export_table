# Export Table

## Backend Modul für Contao 3.

Mit dem Modul lassen sich Contao Tabellen im csv/xml-Format exportieren. Die Export-Klasse lässt sich auch ohne Backend-Modul direkt in eigenen Erweiterungen nutzen. Hier zwei Beispiele:



```php
<?php

// Einfachen CSV-Export initieren (Datei wird zum Browser gesendet)
\MCupic\ExportTable::exportTable('tl_member');


// Etwas komplizierterer Export:
$options = array(
    // Sortierung
    "strSorting" => "city ASC",
    // xml oder csv
    "exportType" => "csv",
    // Datensätze getrennt durch ';'
    "strSeperator" => ";",
    // Felder eingeschlossen von
    "strEnclosure" => '"',
    // arrFilter: Export auf bestimmte Datensätze beschränken
    "arrFilter" => array(array("published=?",1),array("country=?","ch")),
    // strDestinationCharset z.B.: "UTF-8", "ASCII", "Windows-1252", "ISO-8859-15", "ISO-8859-1", "ISO-8859-6", "CP1256"
    "strDestinationCharset" => "Windows-1252",
    // Datei im Contao Fielsystem abspeichern
    "strDestination" => "files/tl_member_backups",
    // Export auf bestimmte Felder beschränken
    "arrSelectedFields" => array("firstname", "lastname", "street", "city", "gender", "email")
);
\MCupic\ExportTable::exportTable('tl_member', $options);

```


Viel Spass mit Export Table!

