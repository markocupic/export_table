<?php
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 * @package export_table
 * @author Marko Cupic 2014
 * @link https://github.com/markocupic/export_table
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace MCupic;

/**
 * Class ExportTable
 * Copyright: 2014 Marko Cupic
 * @author Marko Cupic <m.cupic@gmx.ch>
 * @package export_table
 */


class ExportTable extends \Backend
{


    /**
     * @param $strTable
     * @param array $options
     */
    public static function exportTable($strTable, array $options = array())
    {
        // Defaults
        $preDefinedOptions = array(
            'strSorting' => 'id ASC',
            // Export Type csv or xml
            'exportType' => 'csv',
            'strSeperator' => ';',
            'strEnclosure' => '"',
            // arrFilter array(array("published=?",1),array("pid=6",1))
            'arrFilter' => array(),
            // strDestinationCharset f.ex: "UTF-8", "ASCII", "Windows-1252", "ISO-8859-15", "ISO-8859-1", "ISO-8859-6", "CP1256"
            'strDestinationCharset' => '',
            // strDestination f.ex: files/mydir
            'strDestination' => '',
            // arrSelectedFields f.ex: array('firstname', 'lastname', 'street')
            'arrSelectedFields' => null
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


        $arrData = array();

        // Load Datacontainer
        \Controller::loadDataContainer($strTable, true);
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
        $arrHeadline = array();
        foreach ($arrSelectedFields as $fieldname)
        {
            $arrHeadline[] = $fieldname;
        }
        $arrData[] = $arrHeadline;

        // add rows to $arrData
        if(empty($arrFilter) || !is_array($arrFilter) ){
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
                        $value = $objCallback->$callback[1]($field, $value, $strTable, $dataRecord, $dca);
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
                        $objFile = new \File($objFolder->path . '/' . $strTable . '_' . \Date::parse('Y-m-d_H-i-s') . '.csv');
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
        if ($exportType == 'csv')
        {
            // Write output to file system
            if ($strDestination != '')
            {
                new \Folder($strDestination);
                $objFolder = \FilesModel::findByPath($strDestination);
                if ($objFolder !== null)
                {
                    if ($objFolder->type == 'folder' && is_dir(TL_ROOT . '/' . $objFolder->path))
                    {
                        $objFile = new \File($objFolder->path . '/' . $strTable . '_' . \Date::parse('Y-m-d_H-i-s') . '.csv');

                        foreach ($arrData as $arrRow)
                        {
                            $arrLine = array_map(function ($v) use ($strDestinationCharset)
                            {
                                if ($strDestinationCharset != '')
                                {
                                    $v = iconv("UTF-8", $strDestinationCharset, $v);
                                }
                                return html_entity_decode($v);
                            }, $arrRow);
                            self::fputcsv($objFile->handle, $arrLine, $strSeperator, $strEnclosure);
                        }
                        $objFile->close();
                    }
                }
                return;
            }


            // Send file to browser
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $strTable . ".csv");
            header("Content-Description: csv File");
            header("Pragma: no-cache");
            header("Expires: 0");

            $fh = fopen("php://output", 'w');
            foreach ($arrData as $arrRow)
            {
                $arrLine = array_map(function ($v) use ($strDestinationCharset)
                {
                    if ($strDestinationCharset != '')
                    {
                        $v = iconv("UTF-8", $strDestinationCharset, $v);
                    }
                    return html_entity_decode($v);
                }, $arrRow);
                self::fputcsv($fh, $arrLine, $strSeperator, $strEnclosure);
            }
            fclose($fh);
            exit();
        }
    }


    /**
     * @param $fh
     * @param array $fields
     * @param string $delimiter
     * @param string $enclosure
     * @param bool $mysql_null
     */
    private static function fputcsv($fh, array $fields, $delimiter = ',', $enclosure = '"', $mysql_null = false)
    {

        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        foreach ($fields as $field)
        {
            $field = str_replace('"', '', $field);
            if ($field === null && $mysql_null)
            {
                $output[] = 'NULL';
                continue;
            }

            $output[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field) ? ($enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure) : $field;
        }
        fwrite($fh, implode($delimiter, $output) . "\n");
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
}