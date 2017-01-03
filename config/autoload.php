<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package export_table
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Markocupic\ExportTable',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Markocupic\ExportTable\ExportTable' => 'system/modules/export_table/classes/ExportTable.php',
));
