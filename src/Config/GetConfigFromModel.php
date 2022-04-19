<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Config;

use Contao\FilesModel;
use Contao\StringUtil;
use Markocupic\ExportTable\Model\ExportTableModel;

class GetConfigFromModel
{
    /**
     * @throws \Exception
     */
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
            ->setOutputBom($model->bom)
            ->setFields(StringUtil::deserialize($model->fields, true))
            ->setAddHeadline((bool) $model->addHeadline)
            ->setArrayDelimiter($model->arrayDelimiter)
            ->setActivateDeepLinkExport((bool) $model->activateDeepLinkExport)
            ->setToken($model->token)
            ->setSendFileToTheBrowser((bool) $model->sendFileToTheBrowser)
        ;

        if ('' !== $model->filter) {
            if (!\is_array(json_decode($model->filter))) {
                $message = 'Invalid filter expression. Please insert a JSON array as filter expression: [["tl_calendar_events.published=? AND tl_calendar_events.pid=?"],["1",6]]';

                throw new \Exception($message);
            }

            $config->setFilter(json_decode($model->filter));
        }

        if ($model->saveExport && null !== ($objTargetDir = FilesModel::findOneByUuid($model->saveExportDirectory))) {
            $config->setSaveExport(true);
            $config->setSaveExportDirectory($objTargetDir->uuid);
        }

        if (\strlen((string) $model->filename)) {
            $config->setFilename((string) $model->filename);
        }

        $config->setOverrideFile((bool) $model->overrideFile);

        return $config;
    }
}
