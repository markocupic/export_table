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

use Markocupic\ExportTable\Writer\ByteSequence;
use Ramsey\Uuid\Uuid;
use Contao\DC_Table;
use Contao\DataContainer;

$GLOBALS['TL_DCA']['tl_export_table'] = [
    'config'      => [
        'dataContainer' => DC_Table::class,
        'sql'           => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'        => [
        'sorting'    => [
            'mode'        => DataContainer::MODE_SORTABLE,
            'fields'      => ['exportTable DESC'],
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label'      => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'operations' => [
            'edit'   => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'delete' => [
                'href'       => 'act=delete',
                'icon'       => 'delete.svg',
                'attributes' => 'onclick="onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
            'export' => [
                'href'       => 'action=export',
                'icon'       => 'bundles/markocupicexporttable/export.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['confirmExport'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
        ],
    ],
    'palettes'    => [
        '__selector__' => ['activateDeepLinkExport', 'saveExport'],
        'default'      => '{title_legend},title;'.
            '{settings},exportType,exportTable,fields,addHeadline,sortBy,sortDirection,filter,enclosure,delimiter,arrayDelimiter,sendFileToTheBrowser;'.
            '{save_legend},saveExport;'.
            '{deep_link_legend},activateDeepLinkExport',
        'csv'          => '{title_legend},title;'.
            '{settings},exportType,exportTable,fields,addHeadline,sortBy,sortDirection,filter,enclosure,delimiter,arrayDelimiter,bom,sendFileToTheBrowser;'.
            '{save_legend},saveExport;'.
            '{deep_link_legend},activateDeepLinkExport',
        'xml'          => '{title_legend},title;'.
            '{settings},exportType,exportTable,fields,sortBy,sortDirection,filter,arrayDelimiter,sendFileToTheBrowser;'.
            '{save_legend},saveExport;'.
            '{deep_link_legend},activateDeepLinkExport',
    ],
    'subpalettes' => [
        'activateDeepLinkExport' => 'token,deepLinkInfo',
        'saveExport'             => 'overrideFile,saveExportDirectory,filename',
    ],
    'fields'      => [
        'id'                     => [
            'search' => true,
            'sql'    => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'                 => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'                  => [
            'exclude'   => true,
            'inputType' => 'text',
            'search'    => true,
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'exportType'             => [
            'exclude'   => true,
            'inputType' => 'select',
            'reference' => &$GLOBALS['TL_LANG']['tl_export_table'],
            'eval'      => ['multiple' => false, 'mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(12) NOT NULL default 'csv'",
        ],
        'exportTable'            => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'search'    => true,
            'sorting'   => true,
            'eval'      => ['multiple' => false, 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'fields'                 => [
            'exclude'   => true,
            'inputType' => 'checkboxWizard',
            'eval'      => ['multiple' => true, 'mandatory' => true, 'orderField' => 'orderFields', 'tl_class' => 'clr'],
            'sql'       => 'blob NULL',
        ],
        'addHeadline'            => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'sorting'   => true,
            'eval'      => ['tl_class' => 'clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'orderFields'            => [
            'sql' => 'blob NULL',
        ],
        'sortBy'                 => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'search'    => true,
            'sorting'   => true,
            'eval'      => ['multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'sortDirection'          => [
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['ASC', 'DESC'],
            'eval'      => ['multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'filter'                 => [
            'exclude'   => true,
            'inputType' => 'text',
            'search'    => true,
            'eval'      => ['mandatory' => false, 'rgxp' => 'jsonarray', 'useRawRequestData' => true, 'decodeEntities' => false, 'tl_class' => 'clr'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'enclosure'              => [
            'default'   => '"',
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'text',
            'search'    => true,
            'sorting'   => true,
            'eval'      => ['mandatory' => true, 'maxlength' => 1, 'useRawRequestData' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '\"'",
        ],
        'delimiter'              => [
            'default'   => ';',
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'text',
            'search'    => true,
            'sorting'   => true,
            'eval'      => ['mandatory' => true, 'maxlength' => 1, 'useRawRequestData' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ';'",
        ],
        'arrayDelimiter'         => [
            'default'   => '||',
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'text',
            'search'    => true,
            'sorting'   => true,
            'eval'      => ['mandatory' => true, 'maxlength' => 4, 'useRawRequestData' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(4) NOT NULL default '||'",
        ],
        'bom'                    => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => array_keys(ByteSequence::BOM),
            'search'    => true,
            'sorting'   => true,
            'eval'      => ['includeBlankOption' => true, 'useRawRequestData' => true, 'tl_class' => 'w50'],
            'sql'       => "char(32) NOT NULL default ''",
        ],
        'activateDeepLinkExport' => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'sorting'   => true,
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'token'                  => [
            'default'   => Uuid::uuid4()->toString(),
            'exclude'   => true,
            'inputType' => 'text',
            'search'    => true,
            'eval'      => ['mandatory' => true, 'maxlength' => 250],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'deepLinkInfo'           => [
            'exclude' => true,
            'eval'    => ['doNotShow' => true],
        ],
        'sendFileToTheBrowser'   => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'sorting'   => true,
            'eval'      => ['tl_class' => 'clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'saveExport'             => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'sorting'   => true,
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'overrideFile'           => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'sorting'   => true,
            'eval'      => [],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'saveExportDirectory'    => [
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => ['filesOnly' => false, 'fieldType' => 'radio', 'mandatory' => true],
            'sql'       => 'binary(16) NULL',
        ],
        'filename'               => [
            'exclude'   => true,
            'inputType' => 'text',
            'search'    => true,
            'eval'      => ['maxlength' => 64, 'rgxp' => 'custom', 'customRgxp' => '/^[\w,\s]+$/', 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
    ],
];
