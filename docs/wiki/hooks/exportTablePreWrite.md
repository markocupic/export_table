# exportTablePreWrite Hook
Der **exportTablePreWrite** Hook wird vor dem Befüllen der Export-Datei getriggert.
Es ist die letzte Möglichkeit das Daten-Array zu verändern, oder das Array andersweitig zu verarbeiten.

```php
// App/EventListener/ExportTable/ExportTablePreWriteListener.php

declare(strict_types=1);

namespace App\EventListener\ExportTable;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Listener\ContaoHooks\ExportTableListenerInterface;

 #[AsHook(ExportTablePreWriteListener::HOOK, priority: ExportTablePreWriteListener::PRIORITY)]
class ExportTablePreWriteListener implements ExportTableListenerInterface
{
    public const HOOK = 'exportTablePreWrite';
    public const PRIORITY = 100;
    private static bool $disableHook = false;

    public function __invoke(array $arrData, Config $objConfig): array
    {
        if (static::$disableHook) {
            return $arrData;
        }

        // Do something

        return $arrData;
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
