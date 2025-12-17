<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\EventListener\ContaoHooks;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Markocupic\ExportTable\Config\Config;

#[AsHook(self::HOOK, priority: self::PRIORITY)]
class ExportTableCharsetConversionListener implements ListenerInterface
{
    public const HOOK = 'exportTable';

    public const PRIORITY = 20;

    private static bool $disableHook = false;

    public function __invoke(string $strFieldName, mixed $varValue, string $strTableName, array $arrDataRecord, array $arrDca, Config $objConfig): mixed
    {
        if (static::$disableHook) {
            return $varValue;
        }

        if (\is_string($varValue) && !empty($varValue)) {
            $arrConversionConfig = $objConfig->getConversionConfiguration();

            if ($arrConversionConfig['convertEncoding']) {
                $varValueNew = mb_convert_encoding($varValue, $arrConversionConfig['convertTo'], $arrConversionConfig['convertFrom']);
                $varValue = \is_string($varValueNew) ? $varValueNew : $varValue;
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
