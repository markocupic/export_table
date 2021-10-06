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
use Contao\StringUtil;
use Markocupic\ExportTable\Config\Config;

/**
 * @Hook(ExportTableHandleArraysListener::HOOK, priority=ExportTableHandleArraysListener::PRIORITY)
 */
class ExportTableHandleArraysListener
{
    public const HOOK = 'exportTable';
    public const PRIORITY = 30;

    /**
     * @var bool
     */
    public static $disableHook;

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
        if (static::$disableHook) {
            return $varValue;
        }

        ExportTableFormatDateListener::$disableHook = true;

        $stringUtilAdapter = $this->framework->getAdapter(StringUtil::class);

        if ($arrDca['fields'][$strFieldname]['eval']['csv'] && '' !== $arrDca['fields'][$strFieldname]['eval']['csv']) {
            $delim = $arrDca['fields'][$strFieldname]['csv'];
            $varValue = implode($delim, $stringUtilAdapter->deserialize($varValue, true));
        } elseif (true === $arrDca['fields'][$strFieldname]['eval']['multiple']) {
            $varValue = implode($objConfig->getArrayDelimiter(), $stringUtilAdapter->deserialize($varValue, true));
        }

        return $varValue;
    }
}
