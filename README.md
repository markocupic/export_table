![Markoo Cupic Logo](docs/logo.png?raw=true "logo")

# Export Table für Contao CMS

Mit dieser Erweiterung lassen sich aus dem Contao Backend heraus Datenbank-Tabellen ins CSV- oder XML-Format exportieren. Dabei kann der Export konfiguriert werden.
- Export-Typ auswählen (CSV/XML)
- Tabelle auswählbar
- Felder auswählbar
- Kopfzeile ja/nein
- Über die Eingabe eines json-Arrays Datensätze filtern
- Ausgabe sortierbar (Feldname und Richtung)
- Delimiter einstellbar (Default: ;)
- Enclosure einstellbar (Default: ")
- BOM (für korrekte Darstellung von UTF-8 codierten Zeichen in MS Excel)
- Trennzeichen für Arrays einstellbar
- Deeplink Support
- Speicher-Verzeichnis wählbar
- Dateiname wählbar
- Extension mit HOOKS und weiteren Writern erweiterbar

![Backend](docs/backend.png?raw=true "Backend")

## Der Einsatz von Filtern
Der Export ist über Filter konfigurierbar.

Folgender einfacher Filter für die Mitgliedertabelle *tl_member* lässt nur **Frauen** aus **Luzern** zu:\
`[["gender=? AND city=?"],["female","Luzern"]]`

Oder nur **Frauen** aus **Luzern** oder **Bern**:\
`[["gender=? AND (city=? OR city=?)"],["female","Luzern", "Bern"]]`

Auch der Gebrauch von Contao Insert Tags ist möglich:\
`[["lastname=? AND city=?"],["{{user::lastname}}","Oberkirch"]]`

Oder Parameterübergabe aus der URL:\
`[["lastname=? AND city=?"],["{{GET::lastname}}","Oberkirch"]]`

## Für Entwickler: Die Ausgabe über den "exportTable" HOOK steuern

Via Hook kann die Ausgabe angepasst werden. Die Erweiterung selber nutzt diese Hooks. Beispielsweise werden timestamps vie [exportTable Hook](docs/wiki/hooks/exportTable.md) zu formatierten Daten umgewandelt. Bereits vorhandene Hooks lassen sich über einen eigenen Hook deaktivieren. Dabei muss die Priority so eingestellt werden, dass der neue Hook vor dem bestehenden aufgerufen wird.\
Siehe [siehe dieses Beispiel](docs/wiki/hooks/exportTable.md):


| HOOK                                                            |
| :---                                                            |
| [exportTable](docs/wiki/hooks/exportTable.md)                   |
| [exportTablePreWrite](docs/wiki/hooks/exportTablePreWrite.md)   |
| [exportTablePostWrite](docs/wiki/hooks/exportTablePostWrite.md) |


## ExportTable aus eigenem Controller heraus nutzen
Die ExportTable-Klasse lässt sich recht simpel auch aus anderen Erweiterungen heraus nutzen.

Dazu muss als Erstes der Export konfiguriert werden. Als Konstruktor-Argument wird der Konfigurationsklasse der Name der zu exportierenden Tabelle übergeben. Mit dieser Minimalkonfiguration werden die Default-Einstellungen übernommen. Ein Beispiel mit einer etwas ausführlicheren Konfiguration findest du weiter unten.

```
$config = new Config('tl_member');
```
Der eigentliche Export-Service wird mit der Methode `$this->exportTable->run($objConfig)` aufgerufen, welche als einzigen Parameter das vorher erstellte Config-Objekt erwartet.
```
$config = new Config('tl_member');

return $this->exportTable->run($config);
```

Hier ein etwas ausführlicheres Beispiel eingebettet in einem Custom Controller.

```php
// App/Controller/CustomController.php

declare(strict_types=1);

namespace App\Controller;

use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FilesModel;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Export\ExportTable;
use Markocupic\ExportTable\Writer\Bom;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/_test_export", name="_test_export", defaults={"_scope" = "frontend", "_token_check" = false})
 */
class CustomController extends AbstractController
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var ExportTable
     */
    private $exportTable;

    public function __construct(ContaoFramework $framework, ExportTable $exportTable)
    {
        $this->framework = $framework;
        $this->exportTable = $exportTable;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(): Response
    {
        $this->framework->initialize();

        // First you have to config your data export.
        $config = (new Config('tl_member'))
            ->setExportType('csv')
            ->setFields(['firstname', 'lastname', 'dateOfBirth'])
            ->setAddHeadline(true)
            ->setHeadlineFields(['Vorname', 'Nachname', 'Geburtsdatum'])
            ->setDelimiter(',')
            ->setEnclosure('"')
            // Select * FROM tl_member WHERE tl_member.city = 'Oberkirch'
            ->setFilter([["city=?"],["Oberkirch"]])
            // Save the file to the Contao filesystem
            ->setSaveExport(true)
            // Define a target path, otherwise the file will be temporarily stored in system/tmp
            ->setSaveExportDirectory(FilesModel::findByPath('files')->uuid)
            // Define a filename, otherwise the file will become the name of the table ->tl_member.csv
            ->setFilename('export.csv')
            // Add BOM (correct display of UTF8 encoded chars in MS-Excel)
            ->setBom(Bom::BOM_UTF_8) 
            // Use the row callback to manipulate records
            ->setRowCallback(
                static function ($arrRow) {
                    foreach($arrRow as $fieldName => $varValue)
                    {
                        $arrRow[$fieldName] = doSomething($varValue);
                    }
                    return $arrRow;
                }
            )
        ;

        // The export class takes the config object as single parameter.
        return $this->exportTable->run($config);
    }
}

```

## Erstellen eines Custom-Exporter-Services

Falls die beiden Standard-Writer (CSV und XML) nicht genügen sollten, ist es ohne weiteres möglich einen
weiteren Writer hinzuzufügen. Dazu muss eine Writer Klasse geschrieben werden, die das `Markocupic\ExportTable\Writer\WriterInterface` implementiert.
In `services.yml` muss die Klasse mit `name: markocupic_export_table.writer` getaggt werden. Der Alias sollte eindeutig sein und nicht bereits verwendet worden sein. Z.B. `alias: my_custom_csv`

```yaml

    # Inject Custom CSV writer into Markocupic\ExportTable\Export\ExportTable and Markocupic\ExportTable\DataContainer\ExportTable during compilation
    App\ExportTable\Writer\CustomXmlWriter:
        tags:
            - { name: markocupic_export_table.writer, alias: xml, verbose_name: Custom xml exporter class }
```
Viel Spass mit Export Table!

