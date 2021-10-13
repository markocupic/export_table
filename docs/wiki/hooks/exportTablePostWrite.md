# exportTablePostWrite Hook
Der **exportTablePostWrite** Hook wird nach dem Befüllen der Export-Datei getriggert. Er verlangt als Parameter das File- und Config-Objekt.
Er kann verwendet werden, um z.B. die Datei via Notification zu versenden.

```php
// App/eventListener/ExportTable/ExportTablePostWriteListener.php

declare(strict_types=1);

namespace App\EventListener\ExportTable;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\File;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Listener\ContaoHooks\ExportTableListenerInterface;

/**
 * @Hook(ExportTablePostWriteListener::HOOK, priority=ExportTablePostWriteListener::PRIORITY)
 */
class ExportTablePostWriteListener implements ExportTableListenerInterface
{
    public const HOOK = 'exportTablePostWrite';
    public const PRIORITY = 100;

    /**
     * @var bool
     */
    private static $disableHook = false;

    public function __invoke(File $objFile, Config $objConfig): File
    {
        if (static::$disableHook) {
            return $arrData;
        }

        // Do something

        return $objFile;
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
