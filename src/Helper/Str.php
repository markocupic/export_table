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

namespace Markocupic\ExportTable\Helper;

use Markocupic\ExportTable\Config\Config;

class Str
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function containsInvalidChars(string $str, $arrInvalid): bool
    {
        $str = strtolower($str);

        foreach ($arrInvalid as $strInvalid) {
            if (false !== strpos($str, $strInvalid)) {
                return true;
            }
        }

        return false;
    }
}
