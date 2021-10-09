<?php

declare(strict_types=1);

/*
 * This file is part of Export Table for Contao CMS.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
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
class ExportTableReplaceNewlineListener implements ExportTableListenerInterface
{
    public const HOOK = 'exportTable';
    public const PRIORITY = 20;

    /**
     * @var bool
     */
    private static $disableHook;

    public static function disableHook(): void
    {
        self::$disableHook = true;
    }

    public static function enableHook(): void
    {
        self::$disableHook = false;
    }

    /**
     * @param $varValue
     *
     * @return mixed
     */
    public function __invoke(string $strFieldname, $varValue, string $strTablename, array $arrDataRecord, array $arrDca, Config $objConfig)
    {
        if (static::$disableHook) {
            return $varValue;
        }

        // Replace newlines with [NEWLINE]
        if ($varValue && '' !== $varValue && 'textarea' === $arrDca['fields'][$strFieldname]['inputType']) {
            $varValue = str_replace(PHP_EOL, '[NEWLINE]', (string) $varValue);
        }

        return $varValue;
    }
}
