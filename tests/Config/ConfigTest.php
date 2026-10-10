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

namespace Markocupic\ExportTable\Tests\Config;

use Markocupic\ExportTable\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testHasSensibleDefaults(): void
    {
        $config = new Config('tl_member');

        $this->assertSame('tl_member', $config->getTable());
        $this->assertSame('csv', $config->getExportType());
        $this->assertSame('id', $config->getSortBy());
        $this->assertSame('ASC', $config->getSortDirection());
        $this->assertSame(';', $config->getDelimiter());
        $this->assertSame('"', $config->getEnclosure());
        $this->assertTrue($config->getAddHeadline());
        $this->assertContains('drop', $config->getNotAllowedFilterExpr());
    }

    public function testNormalizesTheSortDirection(): void
    {
        $this->assertSame('DESC', (new Config())->setSortDirection('desc')->getSortDirection());
    }

    public function testRejectsAnInvalidSortDirection(): void
    {
        $this->expectException(\Exception::class);

        (new Config())->setSortDirection('DROP TABLE');
    }

    public function testConfiguresTheEncodingConversion(): void
    {
        $config = (new Config())->convertEncoding(true, 'UTF-8', 'Windows-1252');

        $this->assertSame(['convertEncoding' => true, 'convertFrom' => 'UTF-8', 'convertTo' => 'Windows-1252'], $config->getConversionConfiguration());
    }
}
