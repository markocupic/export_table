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

namespace Markocupic\ExportTable\Listener\ContaoHooks;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Markocupic\ExportTable\Config\Config;

/**
 * @Hook(ExportTableReplaceNewlineListener::HOOK, priority=ExportTableReplaceNewlineListener::PRIORITY)
 */
class ExportTableReplaceNewlineListener implements ListenerInterface
{
    public const HOOK = 'exportTable';
    public const PRIORITY = 200;

    private static bool $disableHook = false;

    /**
     * @param string $strFieldname
     * @param $varValue
     * @param string $strTablename
     * @param array $arrDataRecord
     * @param array $arrDca
     * @param Config $objConfig
     * @return array|mixed|string|string[]|null
     */
    public function __invoke(string $strFieldname, $varValue, string $strTablename, array $arrDataRecord, array $arrDca, Config $objConfig)
    {
        if (static::$disableHook) {
            return $varValue;
        }

        // Replace newlines with [NEWLINE]
        if ($varValue && '' !== $varValue && 'textarea' === $arrDca['fields'][$strFieldname]['inputType']) {
            $varValue = preg_replace('/(?>\r\n|\n|\r)/sm', '[NEWLINE]', (string) $varValue);
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
