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
use Contao\Date;
use Markocupic\ExportTable\Config\Config;

#[AsHook(ExportTableFormatDateListener::HOOK, priority: ExportTableFormatDateListener::PRIORITY)]
class ExportTableFormatDateListener implements ListenerInterface
{
    public const HOOK = 'exportTable';
    public const PRIORITY = 100;
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

        $dateAdapter = $this->framework->getAdapter(Date::class);

        $dca = $arrDca['fields'][$strFieldName] ?? null;

        if ($dca) {
            $strRgxp = $dca['eval']['rgxp'] ?? '';

            if (!empty($varValue) && \in_array($strRgxp, ['date', 'datim', 'time'], true)) {
                try {
                    // Contao tries to retrieve the time/date/date format from the global page object,
                    // but there is none when the app is run in deep link mode.
                    $dateFormat = $dateAdapter->getFormatFromRgxp($strRgxp);
                } catch (\Exception $e) {
                    // Fallback: Retrieve the date/datim-/time-format from config.
                    $configAdapter = $this->framework->getAdapter(\Contao\Config::class);
                    $dateFormat = $configAdapter->get($strRgxp.'Format');
                }

                if (empty($dateFormat)) {
                    throw new \Exception(sprintf('Date-/time format not found for rgxp: %s.', $strRgxp));
                }

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
