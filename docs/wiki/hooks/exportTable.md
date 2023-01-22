# exportTable Hook
Der **exportTable** Hook wird beim Schreiben des Feldinhalts in das Datenarray getriggert. Er kann benutzt werden, um den Feldwert zu verändern.\
Die [Export Table](https://github.com/markocupic/export_table) Erweiterung nutzt den Hook um timestamps in formatierte Daten umzuwandeln.

```php
// App/EventListener/ExportTable/FormatDateListener.php

declare(strict_types=1);

namespace App\EventListener\ExportTable;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Date;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Listener\ContaoHooks\ExportTableFormatDateListener;
use Markocupic\ExportTable\Listener\ContaoHooks\ExportTableListenerInterface;

 #[AsHook(MyCustomFormatDateListener::HOOK, priority: MyCustomFormatDateListener::PRIORITY)]
class MyCustomFormatDateListener implements ExportTableListenerInterface
{
    public const HOOK = 'exportTable';
    public const PRIORITY = 100;
    private static bool $disableHook = false;

    public function __construct(
        private ContaoFramework $framework,
    ) {
    }

    public function __invoke(string $strFieldname, mixed $varValue, string $strTablename, array $arrDataRecord, array $arrDca, Config $objConfig): mixed
    {
        if (static::$disableHook) {
            return $varValue;
        }

        // Disable original Hook that is shipped with the export table extension.
        ExportTableFormatDateListener::disableHook();

        $dateAdapter = $this->framework->getAdapter(Date::class);

        $dca = $arrDca['fields'][$strFieldname] ?? null;

        if ($dca) {
            $strRgxp = $dca['eval']['rgxp'];

            if ('' !== $varValue && $strRgxp && \in_array($strRgxp, ['date', 'datim', 'time'], true)) {
                $dateFormat = $dateAdapter->getFormatFromRgxp($strRgxp);
                $varValue = $dateAdapter->parse($dateFormat, $varValue);
            }
        }

        return $varValue;
    }

    public static function disableHook(): void
    {
        self::$disableHook = true;
    }

    public static function enableHook(): void
    {
        self::$disableHook = false;
    }

    public static function isEnabled(): bool
    {
        return self::$disableHook;
    }
}
```
