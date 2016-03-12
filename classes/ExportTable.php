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
     * @param null $arrSelectedFields
     * @param string $seperator
     * @param string $enclosure
     */
    public static function exportTable($strTable, $strFilter = '', $strSorting = 'id ASC', $arrSelectedFields = null, $exportType = 'csv', $seperator = ';', $enclosure = '"')
    {

        $arrData = array();

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
        $arrFilter = json_decode($strFilter);
        $arrProcedures = [];
        $arrValues = [];
        if (is_array($arrFilter))
        {
            foreach ($arrFilter as $filter)
            {
                $arrProcedures[] = $filter[0];
                $arrValues[] = $filter[1];
            }
        }
        $arrProcedures[] = "id>=?";
        $arrValues[] = 0;


        $objDb = \Database::getInstance()->prepare("SELECT * FROM  " . $strTable . " WHERE " . implode(' AND ', $arrProcedures) . " ORDER BY " . $strSorting)->execute($arrValues);

        //$objDb = \Database::getInstance()->prepare("SELECT * FROM " . $strTable . $strFilter . " ORDER BY id")->execute();
        while ($dataRecord = $objDb->fetchAssoc())
        {
            $arrRow = array();
            foreach ($arrSelectedFields as $field)
            {
                $value = $dataRecord[$field];
                if (is_array(unserialize($value)))
                {
                    $value = implode(',', unserialize($value));
                }

                // HOOK: add custom value
                if (isset($GLOBALS['TL_HOOKS']['exportTable']) && is_array($GLOBALS['TL_HOOKS']['exportTable']))
                {
                    $blnCustomValidation = false;
                    foreach ($GLOBALS['TL_HOOKS']['exportTable'] as $callback)
                    {
                        $objCallback = \System::importStatic($callback[0]);
                        $value = $objCallback->$callback[1]($field, $value, $strTable, $dataRecord);
                    }
                }
                $arrRow[] = $value;
            }
            $arrData[] = $arrRow;
        }


        // xml-output
        if ($exportType == 'xml')
        {
            $x = new \XMLWriter();
            $x->openMemory();
            $x->startDocument('1.0', 'UTF-8');

            $x->startElement($strTable);

            $row = 0;
            foreach ($arrData as $arrRow)
            {
                if ($row == 0)
                {
                    $row++;
                    continue;
                }
                $row++;
                $x->startElement('datarecord');
                $i = 0;
                foreach ($arrRow as $fieldvalue)
                {
                    $x->startElement($arrHeadline[$i]);

                    // Convert bin to uuid
                    if (\Validator::isBinaryUuid($fieldvalue))
                    {
                        $fieldvalue = \StringUtil::binToUuid($fieldvalue);
                    }

                    // Decode html entities
                    $fieldvalue = html_entity_decode($fieldvalue);

                    $x->text($fieldvalue);
                    $x->endElement();
                    $i++;
                }
                $x->endElement();
            }

            $x->endElement();
            $x->endDocument();
            $xml = $x->outputMemory();

            header('Content-type: text/xml');
            header('Content-Disposition: attachment; filename="' . $strTable . '.xml"');
            echo $xml;
            exit();
        }

        // csv-output
        if ($exportType == 'csv')
        {
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $strTable . ".csv");
            header("Content-Description: csv File");
            header("Pragma: no-cache");
            header("Expires: 0");

            $outputBuffer = fopen("php://output", 'w');
            foreach ($arrData as $arrRow)
            {
                $arrLine = array_map(function ($v)
                {
                    //return utf8_decode($v);

                    // Convert bin to uuid
                    if (\Validator::isBinaryUuid($v))
                    {
                        $v = \StringUtil::binToUuid($v);
                    }
                    return html_entity_decode(iconv("UTF-8", "WINDOWS-1252", $v));
                }, $arrRow);
                self::fputcsv($outputBuffer, $arrLine, $seperator, $enclosure);
            }
            fclose($outputBuffer);
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
            if ($field === null && $mysql_null)
            {
                $output[] = 'NULL';
                continue;
            }

            $output[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field) ? ($enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure) : $field;
        }

        fwrite($fh, join($delimiter, $output) . "\n");
    }
}