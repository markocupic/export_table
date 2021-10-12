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

use Ramsey\Uuid\Uuid;

$GLOBALS['TL_DCA']['tl_export_table'] = array(
	'config'      => array(
		'dataContainer' => 'Table',
		'sql'           => array(
			'keys' => array(
				'id' => 'primary',
			),
		),
	),
	'list'        => array(
		'sorting'    => array(
			'fields' => array('tstamp DESC'),
		),
		'label'      => array(
			'fields' => array('title', 'export_table'),
			'format' => '%s Tabelle: %s',
		),
		'operations' => array(
			'edit'   => array(
				'href' => 'act=edit',
				'icon' => 'edit.gif',
			),
			'delete' => array(
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
			),
			'show'   => array(
				'href' => 'act=show',
				'icon' => 'show.gif',
			),
			'export' => array
			(
				'href'                => 'action=export',
				'icon'                => 'bundles/markocupicexporttable/export.png',
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['confirmExport'] . '\')) return false; Backend.getScrollOffset();"',
			),
		),
	),
	'palettes'    => array(
		'__selector__' => array('activateDeepLinkExport', 'saveExport'),
		'default'      => '{title_legend},title;' .
			'{settings},exportType,table,fields,sortBy,sortDirection,filter,enclosure,delimiter,arrayDelimiter,sendFileToTheBrowser;' .
			'{save_legend},saveExport;' .
			'{deep_link_legend},activateDeepLinkExport',
		'csv'      => '{title_legend},title;' .
			'{settings},exportType,table,fields,sortBy,sortDirection,filter,enclosure,delimiter,arrayDelimiter,sendFileToTheBrowser;' .
			'{save_legend},saveExport;' .
			'{deep_link_legend},activateDeepLinkExport',
		'xml'      => '{title_legend},title;' .
			'{settings},exportType,table,fields,sortBy,sortDirection,filter,arrayDelimiter,sendFileToTheBrowser;' .
			'{save_legend},saveExport;' .
			'{deep_link_legend},activateDeepLinkExport',
	),
	'subpalettes' => array(
		'activateDeepLinkExport' => 'token,deepLinkInfo',
		'saveExport'             => 'overrideFile,saveExportDirectory,filename'
	),
	'fields'      => array(
		'id'                     => array(
			'search' => true,
			'sql'    => "int(10) unsigned NOT NULL auto_increment",
		),
		'tstamp'                 => array(
			'sql' => "int(10) unsigned NOT NULL default '0'",
		),
		'title'                  => array(
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'exportType'             => array(
			'inputType' => 'select',
			'options'   => array('csv', 'xml'),
			'eval'      => array('multiple' => false, 'mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(12) NOT NULL default 'csv'",
		),
		'table'                  => array(
			'inputType' => 'select',
			'eval'      => array('multiple' => false, 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'fields'                 => array(
			'inputType' => 'checkboxWizard',
			'eval'      => array('multiple' => true, 'mandatory' => true, 'orderField' => 'orderFields', 'tl_class' => 'clr'),
			'sql'       => "blob NULL",
		),
		'orderFields'            => array(
			'sql' => "blob NULL",
		),
		'sortBy'                 => array(
			'inputType' => 'select',
			'eval'      => array('multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(64) NOT NULL default ''",
		),
		'sortDirection'          => array(
			'inputType' => 'select',
			'options'   => array('ASC', 'DESC'),
			'eval'      => array('multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(64) NOT NULL default ''",
		),
		'filter'                 => array(
			'inputType' => 'text',
			'eval'      => array('mandatory' => false, 'rgxp' => 'jsonarray', 'useRawRequestData' => true, 'decodeEntities' => false, 'tl_class' => 'clr'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'enclosure'              => array(
			'exclude'   => true,
			'default'   => '"',
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 1, 'tl_class' => 'w50', 'useRawRequestData' => true),
			'sql'       => "char(1) NOT NULL default '\"'",
		),
		'delimiter'              => array(
			'exclude'   => true,
			'default'   => ';',
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 1, 'tl_class' => 'w50', 'useRawRequestData' => true),
			'sql'       => "char(1) NOT NULL default ';'",
		),
		'arrayDelimiter'         => array(
			'exclude'   => true,
			'default'   => '||',
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 4, 'tl_class' => 'w50', 'useRawRequestData' => true),
			'sql'       => "varchar(4) NOT NULL default '||'",
		),
		'activateDeepLinkExport' => array(
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'token'                  => array(
			'exclude'   => true,
			'search'    => true,
			'default'   => Uuid::uuid4()->toString(),
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 250),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'deepLinkInfo'           => array(
			'eval' => array('doNotShow' => true),
		),
		'sendFileToTheBrowser'   => array(
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr'),
			'sql'       => "char(1) NOT NULL default '1'",
		),
		'saveExport'             => array(
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'overrideFile'           => array(
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array(),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'saveExportDirectory'    => array(
			'exclude'   => true,
			'inputType' => 'fileTree',
			'eval'      => array('filesOnly' => false, 'fieldType' => 'radio', 'mandatory' => true),
			'sql'       => "binary(16) NULL"
		),
		'filename'               => array(
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('maxlength' => 64, 'rgxp' => 'custom', 'customRgxp' => '/^[\w,\s]+$/', 'tl_class' => 'w50'),
			'sql'       => "varchar(64) NOT NULL default ''",
		),
	),
);
