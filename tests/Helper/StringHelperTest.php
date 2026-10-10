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

namespace Markocupic\ExportTable\Tests\Helper;

use Markocupic\ExportTable\Helper\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function testTestsAgainstASetOfStrings(): void
    {
        $helper = new StringHelper();

        $this->assertTrue($helper->testAgainstSet('gender = ?; drop table tl_member', ['delete', 'drop']));
        $this->assertFalse($helper->testAgainstSet('gender = ? and city = ?', ['delete', 'drop']));
    }
}
