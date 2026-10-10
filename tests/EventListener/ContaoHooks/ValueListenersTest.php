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

namespace Markocupic\ExportTable\Tests\EventListener\ContaoHooks;

use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\EventListener\ContaoHooks\ExportTableCharsetConversionListener;
use Markocupic\ExportTable\EventListener\ContaoHooks\ExportTableEntityDecodeListener;
use Markocupic\ExportTable\EventListener\ContaoHooks\ExportTableReplaceNewlineListener;
use Markocupic\ExportTable\EventListener\ContaoHooks\ExportTableTrimValuesListener;
use PHPUnit\Framework\TestCase;

class ValueListenersTest extends TestCase
{
    public function testTrimsValues(): void
    {
        $this->assertSame('foo', (new ExportTableTrimValuesListener())('name', '  foo ', 'tl_member', [], [], new Config()));
        $this->assertSame(5, (new ExportTableTrimValuesListener())('id', 5, 'tl_member', [], [], new Config()));
    }

    public function testReplacesNewlinesInTextareas(): void
    {
        $dca = ['fields' => ['text' => ['inputType' => 'textarea']]];

        $this->assertSame('a[NEWLINE]b[NEWLINE]c', (new ExportTableReplaceNewlineListener())('text', "a\r\nb\nc", 'tl_member', [], $dca, new Config()));
        $this->assertSame("a\nb", (new ExportTableReplaceNewlineListener())('name', "a\nb", 'tl_member', [], $dca, new Config()));
    }

    public function testDecodesEntities(): void
    {
        $this->assertSame('Müller & Söhne "AG"', (new ExportTableEntityDecodeListener())('company', 'Müller &amp; Söhne &quot;AG&quot;', 'tl_member', [], [], new Config()));
    }

    public function testConvertsTheCharsetIfEnabled(): void
    {
        $listener = new ExportTableCharsetConversionListener();

        $this->assertSame('Zürich', $listener('city', 'Zürich', 'tl_member', [], [], new Config()));

        $config = (new Config())->convertEncoding(true, 'UTF-8', 'ISO-8859-1');

        $this->assertSame(mb_convert_encoding('Zürich', 'ISO-8859-1', 'UTF-8'), $listener('city', 'Zürich', 'tl_member', [], [], $config));
    }
}
