<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Writer;

class ByteSequence
{
    public const BOM = [
        'UTF-8' => "\xEF\xBB\xBF",
        'UTF-16-BE' => "\xFE\xFF",
        'UTF-16-LE' => "\xFF\xFE",
        'UTF-32-BE' => "\x00\x00\xFE\xFF",
        'UTF-32-LE' => "\xFF\xFE\x00\x00",
    ];
}
