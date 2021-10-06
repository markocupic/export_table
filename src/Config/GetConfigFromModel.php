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
        return (new Config())
            ->setTable($model->exportTable)
            ->setExportType($model->exportType)
            ->setTable($model->exportTable)
            ->setSortBy($model->sortBy)
            ->setSortDirection($model->sortDirection)
            ->setEnclosure($model->enclosure)
            ->setDelimiter($model->delimiter)
            ->setFields(StringUtil::deserialize($model->fields, true))
            ->setArrayDelimiter($model->arrayDelimiter)
            ->setFilter($model->filterExpression)
            ->setActivateDeepLinkExport((bool)$model->activateDeepLinkExport)
            ->setDeepLinkExportKey($model->deepLinkExportKey)
        ;
    }
}
