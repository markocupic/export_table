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
 * Run in a custom namespace, so the class can be replaced
 */

namespace Markocupic\ExportTable;

use League\Csv\Reader;
use League\Csv\Writer;

/**
 * Class ExportTable
 * Copyright: 2017 Marko Cupic
 * @author Marko Cupic <m.cupic@gmx.ch>
 * @package export_table
 */
class ExportTable extends \Backend
{


    /**
     * @todo delete destination charset
     * @param null $id
     */
    public static function prepareExport($id = null)
    {
        if ($id == null)
        {
            // When using backend-download ($_POST-Data)
            $strTable = \Input::post('export_table');
            $arrSelectedFields = \Input::post('fields');
            $filterExpression = trim($_POST['filterExpression']);
            $exportType = \Input::post('exportType');
            $destinationCharset = \Input::post('destinationCharset');

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
        }
        else
        {
            // Deep link export requires an id
            $objDb = \Database::getInstance()->prepare('SELECT * FROM tl_export_table WHERE id=?')->execute(\Input::get('id'));
            if ($objDb->numRows)
            {
                if ($objDb->activateDeepLinkExport && \Input::get('key') == $objDb->deepLinkExportKey)
                {
                    $strTable = $objDb->export_table;
                    $arrSelectedFields = deserialize($objDb->fields, true);
                    $filterExpression = trim($objDb->filterExpression);
                    $exportType = $objDb->exportType;
                    $destinationCharset = $objDb->destinationCharset;

                    if (strpos(strtolower($filterExpression), 'delete') !== false || strpos(strtolower($filterExpression), 'update') !== false)
                    {
                        $filterExpression = '';
                    }

                    $sortingExpression = '';
                    if ($objDb->sortBy != '' && $objDb->sortByDirection != '')
                    {
                        $sortingExpression = $objDb->sortBy . ' ' . $objDb->sortByDirection;
                    }
                }
            }

        }


        $options = array(
            'strSorting'            => $sortingExpression,
            'exportType'            => $exportType,
            'strSeperator'          => ';',
            'strEnclosure'          => '"',
            'arrFilter'             => $filterExpression != '' ? json_decode($filterExpression) : array(),
            'strDestinationCharset' => $destinationCharset,
            'strDestination'        => null,
            'arrSelectedFields'     => $arrSelectedFields,
            'useLabelForHeadline'   => null,
        );
        // Call Export class
        self::exportTable($strTable, $options);
    }


    /**
     * @param $strTable
     * @param array $options
     */
    public static function exportTable($strTable, array $options = array())
    {
        // Defaults
        $preDefinedOptions = array(
            'strSorting'            => 'id ASC',
            // Export Type csv or xml
            'exportType'            => 'csv',
            'strSeperator'          => ';',
            'strEnclosure'          => '"',
            // arrFilter array(array("published=?",1),array("pid=6",1))
            'arrFilter'             => array(),
            // strDestinationCharset f.ex: "UTF-8", "ASCII", "Windows-1252", "ISO-8859-15", "ISO-8859-1", "ISO-8859-6", "CP1256"
            'strDestinationCharset' => null,
            // strDestination f.ex: files/mydir
            'strDestination'        => null,
            // arrSelectedFields f.ex: array('firstname', 'lastname', 'street')
            'arrSelectedFields'     => null,
            // useLabelForHeadline: can be null or en, de, fr, ...
            'useLabelForHeadline'   => null,
        );
        $options = array_merge($preDefinedOptions, $options);
        $strSorting = $options['strSorting'];
        $exportType = $options['exportType'];
        $strSeperator = $options['strSeperator'];
        $strEnclosure = $options['strEnclosure'];
        $arrFilter = $options['arrFilter'];
        $strDestinationCharset = $options['strDestinationCharset'];
        $strDestination = $options['strDestination'];
        $arrSelectedFields = $options['arrSelectedFields'];
        $useLabelForHeadline = $options['useLabelForHeadline'];


        $arrData = array();

        // Load Datacontainer
        if (!is_array($GLOBALS['TL_DCA'][$strTable]))
        {
            \Controller::loadDataContainer($strTable, true);
        }

        $dca = array();
        if (is_array($GLOBALS['TL_DCA'][$strTable]))
        {
            $dca = $GLOBALS['TL_DCA'][$strTable];
        }

        // If no fields are selected, then list the whole table
        if ($arrSelectedFields === null || empty($arrSelectedFields))
        {
            $arrSelectedFields = \Database::getInstance()->getFieldNames($strTable);
        }

        // create headline
        if ($useLabelForHeadline !== null)
        {
            // Use language file
            \Controller::loadLanguageFile($strTable, $useLabelForHeadline);
        }

        $arrHeadline = array();
        foreach ($arrSelectedFields as $fieldname)
        {
            $arrLang = $GLOBALS['TL_DCA'][$strTable]['fields'][$fieldname]['label'];
            if (is_array($arrLang) && isset($arrLang[0]))
            {
                $arrHeadline[] = strlen($arrLang[0]) ? $arrLang[0] : $fieldname;
            }
            else
            {
                $arrHeadline[] = $fieldname;
            }
        }
        $arrData[] = $arrHeadline;

        // add rows to $arrData
        if (empty($arrFilter) || !is_array($arrFilter))
        {
            $arrFilter = array();
        }
        $arrProcedures = [];
        $arrValues = [];
        foreach ($arrFilter as $filter)
        {
            $arrProcedures[] = $filter[0];
            $arrValues[] = $filter[1];
        }

        $arrProcedures[] = "id>=?";
        $arrValues[] = 0;
        $arrFieldInfo = self::listFields($strTable);


        $objDb = \Database::getInstance()->prepare("SELECT * FROM  " . $strTable . " WHERE " . implode(' AND ', $arrProcedures) . " ORDER BY " . $strSorting)->execute($arrValues);

        while ($dataRecord = $objDb->fetchAssoc())
        {
            $arrRow = array();
            foreach ($arrSelectedFields as $field)
            {
                $value = $dataRecord[$field];

                // Handle binaries
                if ($value != '')
                {
                    switch (strtolower($arrFieldInfo[$field]['type']))
                    {
                        case 'binary':
                        case 'varbinary':
                        case 'blob':
                        case 'tinyblob':
                        case 'mediumblob':
                        case 'longblob':
                            $value = "0x" . bin2hex($value);
                            break;
                        default:
                            //
                            break;
                    }
                }

                // HOOK: add custom value
                if (isset($GLOBALS['TL_HOOKS']['exportTable']) && is_array($GLOBALS['TL_HOOKS']['exportTable']))
                {
                    foreach ($GLOBALS['TL_HOOKS']['exportTable'] as $callback)
                    {
                        $objCallback = \System::importStatic($callback[0]);
                        $value = $objCallback->{$callback[1]}($field, $value, $strTable, $dataRecord, $dca);
                    }
                }

                $arrRow[] = $value;
            }
            $arrData[] = $arrRow;
        }


        // xml-output
        if ($exportType == 'xml')
        {
            $objXml = new \XMLWriter();
            $objXml->openMemory();
            $objXml->setIndent(true);
            $objXml->setIndentString("\t");
            $objXml->startDocument('1.0', $strDestinationCharset != '' ? $strDestinationCharset : 'UTF-8');

            $objXml->startElement($strTable);

            foreach ($arrData as $row => $arrRow)
            {
                // Headline
                if ($row == 0)
                {
                    continue;
                }

                // New row
                $objXml->startElement('datarecord');
                //$objXml->writeAttribute('index', $row);

                foreach ($arrRow as $i => $fieldvalue)
                {
                    // New field
                    $objXml->startElement($arrHeadline[$i]);

                    // Write Attributes
                    //$objXml->writeAttribute('name', $arrHeadline[$i]);
                    //$objXml->writeAttribute('type', gettype($fieldvalue));
                    //$objXml->writeAttribute('origtype', $arrFieldInfo[$arrHeadline[$i]]['type']);

                    // Convert to charset
                    if ($strDestinationCharset != '')
                    {
                        $fieldvalue = iconv("UTF-8", $strDestinationCharset, $fieldvalue);
                    }

                    if (is_numeric($fieldvalue) || is_null($fieldvalue) || $fieldvalue == '')
                    {
                        $objXml->text($fieldvalue);
                    }
                    else
                    {
                        // Write CDATA
                        $objXml->writeCdata($fieldvalue);
                    }

                    $objXml->endElement();
                    //end field-tag
                }
                $objXml->endElement();
                // End row-tag
            }

            $objXml->endElement();
            // End table-tag

            $objXml->endDocument();
            $xml = $objXml->outputMemory();

            // Write output to file system
            if ($strDestination != '')
            {
                new \Folder($strDestination);
                $objFolder = \FilesModel::findByPath($strDestination);
                if ($objFolder !== null)
                {
                    if ($objFolder->type == 'folder' && is_dir(TL_ROOT . '/' . $objFolder->path))
                    {

                        $objFile = new \File($objFolder->path . '/' . $strTable . '_' . \Date::parse('Y-m-d_H-i-s') . '.xml', false);
                        $objFile->write($xml);
                        $objFile->close();
                        return;
                    }
                }
            }

            // Send file to browser
            header('Content-type: text/xml');
            header('Content-Disposition: attachment; filename="' . $strTable . '.xml"');
            echo $xml;
            exit();
        }

        // csv-output
        if ($exportType === 'csv')
        {
            // Write output to file system
            if ($strDestination !== null)
            {
                if (!is_dir(TL_ROOT . '/' . $strDestination))
                {
                    mkdir(TL_ROOT . '/' . $strDestination);
                }

                if (is_dir(TL_ROOT . '/' . $strDestination))
                {

                        $objFile = new \File($strDestination . '/' . $strTable . '_' . \Date::parse('Y-m-d_H-i-s') . '.csv');
                        $objFile->write('');
                        // Convert special chars
                        $arrFinal = array();
                        foreach ($arrData as $arrRow)
                        {
                            $arrLine = array_map(function ($v) use ($strDestinationCharset) {
                                return html_entity_decode(htmlspecialchars_decode($v));
                            }, $arrRow);
                            $arrFinal[] = $arrLine;
                        }


                        // Load the CSV document from a string
                        $csv = Writer::createFromString('');
                        $csv->setOutputBOM(Reader::BOM_UTF8);

                        $csv->setDelimiter($strSeperator);
                        $csv->setEnclosure($strEnclosure);

                        // Insert all the records
                        $csv->insertAll($arrFinal);

                        // Write csv into file
                        $objFile->write($csv);
                        $objFile->close();
                        exit;

                }
                return;
            }

            // Convert special chars
            $arrFinal = array();
            foreach ($arrData as $arrRow)
            {
                $arrLine = array_map(function ($v) use ($strDestinationCharset) {
                    return html_entity_decode(htmlspecialchars_decode($v));
                }, $arrRow);
                $arrFinal[] = $arrLine;
            }

            // Send file to browser
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $strTable . '.csv"');

            // Load the CSV document from a string
            $csv = Writer::createFromString('');
            $csv->setOutputBOM(Reader::BOM_UTF8);

            $csv->setDelimiter($strSeperator);
            $csv->setEnclosure($strEnclosure);

            // Insert all the records
            $csv->insertAll($arrFinal);

            // Returns the CSV document as a string
            echo $csv;
            exit;

        }
    }

    /**
     * @param $strTable
     * @return array
     */
    public static function listFields($strTable)
    {
        $objDb = \Database::getInstance();
        $arrFields = $objDb->listFields($strTable);
        $arrNew = array();
        if (is_array($arrFields))
        {
            foreach ($arrFields as $arrField)
            {
                $arrNew[$arrField['name']] = $arrField;
            }
        }
        return $arrNew;
    }



    /**
     * array_map for multidimensional arrays
     * @param $array
     * @return array|string
     */
    private static function array_map_deep($array)
    {
        $new = array();
        if (is_array($array))
        {
            foreach ($array as $key => $val)
            {
                if (is_array($val))
                {
                    $new[$key] = self::array_map_deep($val);
                }
                else
                {
                    $new[$key] = self::binToUuid($val);
                }
            }
        }
        else
        {
            $new = self::binToUuid($array);
        }
        return $new;
    }

    /**
     * @param $value
     * @return string
     */
    public static function binToUuid($value)
    {
        // Convert bin to uuid
        if (\Validator::isBinaryUuid($value))
        {
            return \StringUtil::binToUuid($value);
        }
        return $value;
    }

}
