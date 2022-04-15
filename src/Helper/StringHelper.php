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

namespace Markocupic\ExportTable\Helper;

class StringHelper
{
    public function testAgainstSet(string $strTest, $arrStrings): bool
    {
        foreach ($arrStrings as $str) {
            if (false !== strpos($strTest, $str)) {
                return true;
            }
        }

        return false;
    }
}
