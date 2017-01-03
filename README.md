# Export Table

## Tabellen-Export-Modul für Contao 3 und Contao 4

Mit dem Modul lassen sich Contao Tabellen im csv/xml-Format exportieren. Mit dem exportTable-Hook kann der Feldinhalt angepasst werden.
Erstelle dazu in system/modules ein neues Verzeichnis: aaa_export_table_hooks. Darin erstellst du in den entsprechenden Verzeichnissen die beiden php-Dateien. Anschliessend noch den autoload-Creatoor im Backend laufen lassen.
```php
<?php
// system/modules/aaa_export_table_hooks/config/config.php
$GLOBALS['TL_HOOKS']['exportTable'][] = array('Markocupic\ExportTable\ExportTableHook', 'exportTableHook');

```

```php
<?php
// system/modules/aaa_export_table_hooks/classes/ExportTableHook.php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 * @package csv_export
 * @author Marko Cupic 2017
 * @link https://github.com/markocupic/csv_export
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Markocupic\ExportTable;



/**
 * Class ExportTableHook
 * Copyright: 2017 Marko Cupic
 * @author Marko Cupic <m.cupic@gmx.ch>
 */


class ExportTableHook
{

    /**
     * @param $field
     * @param string $value
     * @param $table
     * @param $dataRecord
     * @param $dca
     * @return string
     */
    public static function exportTableHook($field, $value = '', $table, $dataRecord, $dca)
    {
        if ($table == 'tl_calendar_events')
        {
            if ($field == 'startDate' || $field == 'endDate' || $field == 'tstamp')
            {
                if ($value > 0)
                {
                    $value = \Date::parse('d.m.Y', $value);
                }
            }
        }
        return $value;
    }
}

```

Wichtig!!!
Versichere dich, dass der Hook-Container vor dem export_table Container geladen wird. In Contao 4 erreichst du dies, indem du in der AppKernel.php den Hook-Container vor dem export_table-Container registrierst. 


```php
<?php
// app/AppKernel.php
<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            // ...other
            new Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle(('aaa_export_table_hooks'), $this->getRootDir()),
            new Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle(('export_table'), $this->getRootDir()),
            // ..other


        ];

        // .....

        return $bundles;
    }

```
Viel Spass mit Export Table!

