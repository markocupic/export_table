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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Date;
use Markocupic\ExportTable\Config\Config;

/**
 * @Hook(ExportTableFormatDateListener::HOOK, priority=ExportTableFormatDateListener::PRIORITY)
 */
class ExportTableFormatDateListener
{
    public const HOOK = 'exportTable';
    public const PRIORITY = 10;

    /**
     * @var bool
     */
    public static $disableHook = false;

    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * @param $varValue
     *
     * @return mixed
     */
    public function __invoke(string $strFieldname, $varValue, string $strTablename, array $arrDataRecord, array $arrDca, Config $objConfig)
    {
        if(static::$disableHook){
            return $varValue;
        }

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
}
