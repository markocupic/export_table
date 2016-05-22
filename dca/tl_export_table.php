<?php
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 * @package export_table
 * @author Marko Cupic 2014
 * @link    http://www.contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 * @link https://github.com/markocupic/export_table
 */

$GLOBALS['TL_DCA']['tl_export_table'] = array(
    // Config
    'config' => array(
        'dataContainer' => 'Table',
        'sql' => array(
            'keys' => array(
                'id' => 'primary',
            )
        ),
    ),
    // List
    'list' => array(
        'sorting' => array(
            'fields' => array('tstamp DESC'),
        ),
        'label' => array(
            'fields' => array('export_table'),
            'format' => '%s'
        ),
        'global_operations' => array(),
        'operations' => array(
            'edit' => array(
                'label' => &$GLOBALS['TL_LANG']['MSC']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ),
            'delete' => array(
                'label' => &$GLOBALS['TL_LANG']['MSC']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'show' => array(
                'label' => &$GLOBALS['TL_LANG']['MSC']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif'
            )
        )
    ),
    // Palettes
    'palettes' => array(
        'default' => '{settings},export_table,exportType,fields,destinationCharset,filterExpression,sortBy,sortByDirection',
    ),
    // Fields
    'fields' => array(

        'id' => array(
            'label' => array('ID'),
            'search' => true,
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),

        'tstamp' => array(
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),

        'export_table' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_export_table']['export_table'],
            'inputType' => 'select',
            'options_callback' => array(
                'tl_export_table',
                'optionsCbGetTables'
            ),
            'eval' => array(
                'multiple' => false,
                'mandatory' => true,
                'includeBlankOption' => true,
                'submitOnChange' => true,
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),

        'filterExpression' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_export_table']['filterExpression'],
            'inputType' => 'text',
            'eval' => array(
                'mandatory' => false, 'preserveTags' => false, 'allowHtml' => true, 'decodeEntities' => false
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),


        'fields' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_export_table']['fields'],
            'inputType' => 'checkbox',
            'options_callback' => array(
                'tl_export_table',
                'optionsCbSelectedFields'
            ),
            'eval' => array(
                'multiple' => true,
                'mandatory' => true,
            ),
            'sql' => "blob NULL",
        ),
        'sortBy' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_export_table']['sortBy'],
            'inputType' => 'select',
            'options_callback' => array(
                'tl_export_table',
                'optionsCbSelectedFields'
            ),
            'eval' => array(
                'multiple' => false,
                'mandatory' => false,
            ),
            'sql' => "blob NULL",
        ),
        'destinationCharset' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_export_table']['destinationCharset'],
            'inputType' => 'select',
            'options' => array("UTF-8", "Windows-1252", "ASCII", "ISO-8859-15", "ISO-8859-1", "ISO-8859-6", "CP1256"),
            'eval' => array(
                'multiple' => false,
                'mandatory' => false,
            ),
            'sql' => "blob NULL",
        ),
        'sortByDirection' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_export_table']['sortByDirection'],
            'inputType' => 'select',
            'options' => array('ASC', 'DESC'),
            'eval' => array(
                'multiple' => false,
                'mandatory' => false,
            ),
            'sql' => "blob NULL",
        ),
        'exportType' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_export_table']['exportType'],
            'inputType' => 'select',
            'options' => array('csv', 'xml'),
            'eval' => array(
                'multiple' => false,
                'mandatory' => false,
            ),
            'sql' => "blob NULL",
        )
    )
);

/**
 * Class tl_export_table
 * Provide miscellaneous methods that are used by the data configuration array.
 * Copyright : &copy; 2014 Marko Cupic
 * @author Marko Cupic 2014
 * @package export_table
 */
class tl_export_table extends Backend
{

    public function __construct()
    {

        parent::__construct();

        if ($_POST['saveNcreate'] && $this->Input->post('FORM_SUBMIT') && $this->Input->post('SUBMIT_TYPE') != 'auto' && !$_SESSION['export_table'])
        {
            unset($_POST['saveNcreate']);
            $this->initExport();
        }
    }


    /**
     * init Export
     */
    private function initExport()
    {
        $strTable = $this->Input->post('export_table');
        $arrSelectedFields = $this->Input->post('fields');

        $filterExpression = trim($_POST['filterExpression']);
        if (strpos(strtolower($filterExpression), 'delete') !== false || strpos(strtolower($filterExpression), 'update') !== false)
        {
            $filterExpression = '';
            $_POST['filterExpression'] = '';
        }
        $sortingExpression = '';
        if ($_POST['sortBy'] != '' && $_POST['sortByDirection'] != '')
        {
            $sortingExpression = $_POST['sortBy'] . ' ' . $_POST['sortByDirection'];
        }
        $options = array(
            'strSorting' => $sortingExpression,
            'exportType' => $this->Input->post('exportType'),
            'strSeperator' => ';',
            'strEnclosure' => '"',
            'arrFilter' => $filterExpression != '' ? json_decode($filterExpression) : array(),
            'strDestinationCharset' => $this->Input->post('destinationCharset'),
            'strDestination' => '',
            'arrSelectedFields' => $arrSelectedFields
        );
        // Call Export class
        MCupic\ExportTable::exportTable($strTable, $options);
    }


    /**
     * option_callback
     * @return array
     */
    public function optionsCbGetTables()
    {

        $objTables = $this->Database->listTables();
        $arrOptions = array();
        foreach ($objTables as $table)
        {
            $arrOptions[] = $table;
        }
        return $arrOptions;
    }


    /**
     * option_callback
     * @return array
     */
    public function optionsCbSelectedFields()
    {

        $objDb = $this->Database->prepare("SELECT * FROM tl_export_table WHERE id = ? LIMIT 0,1")->execute(\Input::get('id'));
        if ($objDb->export_table == '')
        {
            return;
        }
        $objFields = $this->Database->listFields($objDb->export_table, 1);
        $arrOptions = array();
        foreach ($objFields as $field)
        {
            if (in_array($field['name'], $arrOptions))
            {
                continue;
            }
            $arrOptions[$field['name']] = $field['name'] . ' [' . $field['type'] . ']';
        }
        return $arrOptions;
    }


    /**
     * Parse Backend Template Hook
     * @param string
     * @param string
     * @return string
     */
    public function parseBackendTemplate($strContent, $strTemplate)
    {

        if (Input::get('act') == 'edit')
        {
            // remove saveNClose button
            $strContent = preg_replace('/<input type=\"submit\" name=\"saveNclose\"((\r|\n|.)+?)>/', '', $strContent);

            //rename buttons
            $strContent = preg_replace('/<input type=\"submit\" name=\"save\" id=\"save\" class=\"tl_submit\" accesskey=\"s\" value=\"((\r|\n|.)+?)\">/', '<input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="' . $GLOBALS['TL_LANG']['MSC']['save'] . '">', $strContent);
            $strContent = preg_replace('/<input type=\"submit\" name=\"saveNcreate\" id=\"saveNcreate\" class=\"tl_submit\" accesskey=\"n\" value=\"((\r|\n|.)+?)\">/', '<input type="submit" name="saveNcreate" id="saveNcreate" class="tl_submit exportButton" accesskey="n" value="' . $GLOBALS['TL_LANG']['tl_export_table']['launchExportButton'] . '">', $strContent);


            if (strstr($strContent, 'reportTable'))
            {
                $strContent = preg_replace('/<input type=\"submit\" name=\"save\"((\r|\n|.)+?)>/', '', $strContent);
                $strContent = preg_replace('/<input type=\"submit\" name=\"saveNclose\"((\r|\n|.)+?)>/', '', $strContent);
                $strContent = preg_replace('/<input type=\"submit\" name=\"saveNcreate\"((\r|\n|.)+?)>/', '', $strContent);
            }

        }

        return $strContent;
    }
}           
              