# exportTablePreWrite Hook
Der **exportTablePreWrite** Hook wird vor dem Befüllen der Export-Datei getriggert.
Es ist die letzte Möglichkeit das Daten-Array zu verändern, oder das Array andersweitig zu verarbeiten.

```php
// App/eventListener/ExportTable/ExportTablePreWriteListener.php

declare(strict_types=1);

namespace App\EventListener\ExportTable;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Listener\ContaoHooks\ExportTableListenerInterface;

/**
 * @Hook(ExportTablePreWriteListener::HOOK, priority=ExportTablePreWriteListener::PRIORITY)
 */
class ExportTablePreWriteListener implements ExportTableListenerInterface
{
    public const HOOK = 'exportTablePreWrite';
    public const PRIORITY = 100;

    /**
     * @var bool
     */
    private static $disableHook = false;

    public function __invoke(array $arrData, Config $objConfig): array
    {
        if (static::$disableHook) {
            return $arrData;
        }

        // Do something

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
