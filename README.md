# Export Table

## Backend Modul für Contao 3.

Mit dem Modul lassen sich Contao Tabellen im csv/xml-Format exportieren. Die Export-Klasse lässt sich auch in eigenen Erweiterungen einbauen. Hier zwei Beispiele:



```php
<?php

// Einfachen CSV-Export initieren (Datei wird zum Browser gesandt)
\MCupic\ExportTable::exportTable('tl_member');


// Etwas komplizierterer Export:
$options = array(
    "strSorting" => "city ASC",
    // xml oder csv
    "exportType" => "xml",
    "strSeperator" => ";",
    "strEnclosure" => '"',
    // arrFilter array(array("published=?",1),array("pid=6",1))
    "arrFilter" => array(array("published=?",1),array("country=?","ch")),
    // strDestinationCharset z.B.: "UTF-8", "ASCII", "Windows-1252", "ISO-8859-15", "ISO-8859-1", "ISO-8859-6", "CP1256"
    "strDestinationCharset" => "Windows-1252",
    // Datei im Contao Fielsystem abspeichern
    "strDestination" => "files/tl_member_backups",
    "arrSelectedFields" => array("firstname", "lastname", "street", "city", "gender", "email")
);
\MCupic\ExportTable::exportTable('tl_member', $options);

```


Viel Spass mit Export Table!

