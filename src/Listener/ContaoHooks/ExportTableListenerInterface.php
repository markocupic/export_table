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

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Widget;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Helper\Str;

interface ExportTableListenerInterface
{

    public static function disableHook(): void;
    public static function enableHook(): void;

}
