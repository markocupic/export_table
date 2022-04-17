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

namespace Markocupic\ExportTable\Writer;

class Bom
{
    public const UTF_8 = "\xEF\xBB\xBF";
    public const UTF_16_BE = "\xFE\xFF";
    public const UTF_16_LE = "\xFF\xFE";
    public const UTF_32_BE = "\x00\x00\xFE\xFF";
    public const UTF_32_LE = "\xFF\xFE\x00\x00";
}
