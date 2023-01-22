<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Listener\ContaoHooks;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Markocupic\ExportTable\Config\Config;

#[AsHook(ExportTableEntityDecodeListener::HOOK, priority: ExportTableEntityDecodeListener::PRIORITY)]
class ExportTableEntityDecodeListener implements ListenerInterface
{
    public const HOOK = 'exportTable';
    public const PRIORITY = 500;
    private static bool $disableHook = false;

    /**
     * @param $varValue
     *
     * @return mixed|string
     */
    public function __invoke(string $strFieldName, $varValue, string $strTableName, array $arrDataRecord, array $arrDca, Config $objConfig)
    {
        if (static::$disableHook) {
            return $varValue;
        }

        if (\is_string($varValue) && !empty($varValue)) {
            $varValue = html_entity_decode($varValue, ENT_QUOTES);
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
