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

namespace Markocupic\ExportTable\Config;

use Contao\StringUtil;
use Markocupic\ExportTable\Model\ExportTableModel;

class GetConfigFromModel
{
    public function get(ExportTableModel $model): Config
    {
        $config = (new Config($model->table))
            ->setModel($model)
            ->setTitle($model->title)
            ->setExportType($model->exportType)
            ->setTable($model->table)
            ->setSortBy($model->sortBy)
            ->setSortDirection($model->sortDirection)
            ->setEnclosure($model->enclosure)
            ->setDelimiter($model->delimiter)
            ->setFields(StringUtil::deserialize($model->fields, true))
            ->setArrayDelimiter($model->arrayDelimiter)
            ->setActivateDeepLinkExport((bool) $model->activateDeepLinkExport)
            ->setToken($model->token)
        ;

        if ('' !== $model->filter) {
            if (!\is_array(json_decode($model->filter))) {
                $message = 'Invalid filter expression. Please insert a JSON array as filter expression: [["tl_calendar_events.published=? AND tl_calendar_events.pid=?"],["1",6]]';

                throw new \Exception($message);
            }

            $config->setFilter(json_decode($model->filter));
        }

        return $config;
    }
}
