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

namespace Markocupic\ExportTable\Listener\ContaoHooks;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\StringUtil;
use Markocupic\ExportTable\Config\Config;

#[AsHook(ExportTableHandleArraysListener::HOOK, priority: ExportTableHandleArraysListener::PRIORITY)]
class ExportTableHandleArraysListener implements ListenerInterface
{
    public const HOOK = 'exportTable';
    public const PRIORITY = 300;
    private static bool $disableHook = false;

    public function __construct(
        private readonly ContaoFramework $framework,
    ) {
    }

    public function __invoke(string $strFieldName, mixed $varValue, string $strTableName, array $arrDataRecord, array $arrDca, Config $objConfig): mixed
    {
        if (static::$disableHook) {
            return $varValue;
        }

        $stringUtilAdapter = $this->framework->getAdapter(StringUtil::class);

        $dcaEval = $arrDca['fields'][$strFieldName]['eval'] ?? [];

        if (isset($dcaEval['csv']) && '' !== $dcaEval['csv']) {
            $delim = $dcaEval['csv'];
            $varValue = implode($delim, $stringUtilAdapter->deserialize($varValue, true));
        } elseif (isset($dcaEval['multiple']) && true === $dcaEval['multiple']) {
            $varValue = implode($objConfig->getArrayDelimiter(), $stringUtilAdapter->deserialize($varValue, true));
        } elseif (!empty($varValue) && \is_string($varValue) && str_starts_with($varValue, 'a:') && \is_array($stringUtilAdapter->deserialize($varValue)) && !$this->arrIsMultiDimensional($stringUtilAdapter->deserialize($varValue))) {
            $varValue = implode($objConfig->getArrayDelimiter(), StringUtil::deserialize($varValue, true));
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

    /**
     * Function to check if array is
     * multidimensional or not.
     */
    private function arrIsMultiDimensional(array $arr): bool
    {
        rsort($arr);

        return isset($arr[0]) && \is_array($arr[0]);
    }
}
