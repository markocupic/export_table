<?php

/*
 * This file is part of Export Table for Contao CMS.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

// Errors
$GLOBALS['TL_LANG']['ERR']['exportTblInvalidFilterExpression'] = 'Ungültiger Filter-Ausdruck. Bitte geben Sie ein JSON array in dieser Form ein: [["tl_calendar_events.published=? AND tl_calendar_events.pid=?"],["1",6]].';
$GLOBALS['TL_LANG']['ERR']['exportTblNotAllowedFilterExpression'] = 'Der Filter enthält mind. einen unerlaubten Ausdruck! Verwenden Sie nicht "%s" in Ihrem Filter-Ausdruck.';
