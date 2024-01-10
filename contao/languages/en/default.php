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

// Errors
$GLOBALS['TL_LANG']['ERR']['exportTblInvalidFilterExpression'] = 'Invalid filter expression. Please insert a JSON array like this: [["tl_calendar_events.published=? AND tl_calendar_events.pid=?"],["1",6]].';
$GLOBALS['TL_LANG']['ERR']['exportTblNotAllowedFilterExpression'] = 'Illegal filter expression! Do not use "%s" in your filter expression.';

// Misc
$GLOBALS['TL_LANG']['MSC']['savedExportFile'] = 'Saved file to "%s".';
