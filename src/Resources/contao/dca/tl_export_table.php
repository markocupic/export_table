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
		'sorting'           => array(
			'fields' => array('tstamp DESC'),
		),
		'label'             => array(
			'fields' => array('title', 'export_table'),
			'format' => '%s Tabelle: %s',
		),
		'operations'        => array(
			'edit'   => array(
				'label' => &$GLOBALS['TL_LANG']['MSC']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			),
			'delete' => array(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
			),
			'show'   => array(
				'label' => &$GLOBALS['TL_LANG']['MSC']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif',
			),
		),
	),
	'palettes'    => array(
		'__selector__' => array('activateDeepLinkExport'),
		'default'      => '{title_legend},title;{settings},exportType,exportTable,fields,sortBy,sortDirection,filterExpression,enclosure,delimiter,arrayDelimiter;{deep_link_legend},activateDeepLinkExport',
	),
	'subpalettes' => array(
		'activateDeepLinkExport' => 'deepLinkExportKey,deepLinkInfo',
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
			'eval'      => array('multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(12) NOT NULL default 'csv'",
		),
		'exportTable'            => array(
			'inputType' => 'select',
			'eval' => array('multiple' => false, 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'),
			'sql'  => "varchar(255) NOT NULL default ''",
		),
		'fields'                 => array(
			'inputType' => 'checkboxWizard',
			'eval' => array('multiple' => true, 'mandatory' => true, 'orderField' => 'orderFields', 'tl_class' => 'clr'),
			'sql'  => "blob NULL",
		),
		'orderFields'            => array(
			'sql'   => "blob NULL",
		),
		'sortBy'                 => array(
			'inputType' => 'select',
			'eval'      => array('multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "blob NULL",
		),
		'sortDirection'          => array(
			'inputType' => 'select',
			'options'   => array('ASC', 'DESC'),
			'eval'      => array('multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'),
            'sql'       => "varchar(64) NOT NULL default ''",
		),
		'filterExpression'       => array(
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
		'arrayDelimiter'              => array(
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
		'deepLinkExportKey'      => array(
			'exclude'   => true,
			'search'    => true,
			'default'   => md5(microtime() . random_int(0, getrandmax())),
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 250),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'deepLinkInfo'           => array(
			'eval' => array('doNotShow' => true),
		),
	),
);
