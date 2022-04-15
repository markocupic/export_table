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

use Ramsey\Uuid\Uuid;

$GLOBALS['TL_DCA']['tl_export_table'] = [
    'config'      => [
        'dataContainer' => 'Table',
        'sql'           => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'        => [
        'sorting'    => [
            'fields' => ['tstamp DESC'],
        ],
        'label'      => [
            'fields' => ['title', 'export_table'],
            'format' => '%s Tabelle: %s',
        ],
        'operations' => [
            'edit'   => [
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ],
            'delete' => [
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\')) return false; Backend.getScrollOffset();"',
            ],
            'show'   => [
                'href' => 'act=show',
                'icon' => 'show.gif',
            ],
            'export' => [
                'href'       => 'action=export',
                'icon'       => 'bundles/markocupicexporttable/export.png',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
                'attributes' => 'onclick="if (!confirm(\''.$GLOBALS['TL_LANG']['MSC']['confirmExport'].'\')) return false; Backend.getScrollOffset();"',
            ],
        ],
    ],
    'palettes'    => [
        '__selector__' => ['activateDeepLinkExport', 'saveExport'],
        'default'      => '{title_legend},title;'.
            '{settings},exportType,table,fields,addHeadline,sortBy,sortDirection,filter,enclosure,delimiter,arrayDelimiter,sendFileToTheBrowser;'.
            '{save_legend},saveExport;'.
            '{deep_link_legend},activateDeepLinkExport',
        'csv'          => '{title_legend},title;'.
            '{settings},exportType,table,fields,addHeadline,sortBy,sortDirection,filter,enclosure,delimiter,arrayDelimiter,bom,sendFileToTheBrowser;'.
            '{save_legend},saveExport;'.
            '{deep_link_legend},activateDeepLinkExport',
        'xml'          => '{title_legend},title;'.
            '{settings},exportType,table,fields,sortBy,sortDirection,filter,arrayDelimiter,sendFileToTheBrowser;'.
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
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'exportType'             => [
            'inputType' => 'select',
            'reference' => &$GLOBALS['TL_LANG']['tl_export_table'],
            'eval'      => ['multiple' => false, 'mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(12) NOT NULL default 'csv'",
        ],
        'table'                  => [
            'inputType' => 'select',
            'eval'      => ['multiple' => false, 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'fields'                 => [
            'inputType' => 'checkboxWizard',
            'eval'      => ['multiple' => true, 'mandatory' => true, 'orderField' => 'orderFields', 'tl_class' => 'clr'],
            'sql'       => 'blob NULL',
        ],
        'addHeadline'            => [
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'orderFields'            => [
            'sql' => 'blob NULL',
        ],
        'sortBy'                 => [
            'inputType' => 'select',
            'eval'      => ['multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'sortDirection'          => [
            'inputType' => 'select',
            'options'   => ['ASC', 'DESC'],
            'eval'      => ['multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'filter'                 => [
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'rgxp' => 'jsonarray', 'useRawRequestData' => true, 'decodeEntities' => false, 'tl_class' => 'clr'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'enclosure'              => [
            'exclude'   => true,
            'default'   => '"',
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 1, 'useRawRequestData' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '\"'",
        ],
        'delimiter'              => [
            'exclude'   => true,
            'default'   => ';',
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 1, 'useRawRequestData' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ';'",
        ],
        'arrayDelimiter'         => [
            'exclude'   => true,
            'default'   => '||',
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 4, 'useRawRequestData' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(4) NOT NULL default '||'",
        ],
        'bom'                    => [
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['UTF-8'],
            'eval'      => ['includeBlankOption' => true, 'useRawRequestData' => true, 'tl_class' => 'w50'],
            'sql'       => "char(32) NOT NULL default ''",
        ],
        'activateDeepLinkExport' => [
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'token'                  => [
            'exclude'   => true,
            'search'    => true,
            'default'   => Uuid::uuid4()->toString(),
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 250],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'deepLinkInfo'           => [
            'eval' => ['doNotShow' => true],
        ],
        'sendFileToTheBrowser'   => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'saveExport'             => [
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'overrideFile'           => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
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
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'rgxp' => 'custom', 'customRgxp' => '/^[\w,\s]+$/', 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
    ],
];
