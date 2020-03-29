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

use Contao\Date;
use Contao\File;
use Contao\FilesModel;
use Contao\Folder;
use Contao\Input;
use Contao\StringUtil;
use Contao\Backend;
use Contao\Database;
use Contao\Controller;
use Contao\System;
use Contao\Validator;
use League\Csv\Reader;
use League\Csv\Writer;

/**
 * Class ExportTable
 * Copyright: 2017 Marko Cupic
 * @author Marko Cupic <m.cupic@gmx.ch>
 * @package export_table
 */
class ExportTable extends Backend
{

    /**
     * @throws \Exception
     */
    public static function prepareExport()
    {
        $key = null;

        // Support Deep Link export
        if (Input::get('action') === 'exportTable' && Input::get('key') != '')
        {
            $key = Input::get('key');
        }

        if ($key !== null)
        {
            // Deep link export requires an id
            $objDb = Database::getInstance()->prepare('SELECT * FROM tl_export_table WHERE deepLinkExportKey=?')->execute($key);
        }
        elseif (TL_MODE === 'BE' && Input::get('id') !== '' && Input::get('do') === 'export_table')
        {
            // Deep link export requires an id
            $objDb = Database::getInstance()->prepare('SELECT * FROM tl_export_table WHERE id=?')->execute(Input::get('id'));
        }
        else
        {
            throw new \Exception('You are not allowed to use this service.');
        }

        if ($objDb->numRows)
        {
            if (TL_MODE === 'FE' && !$objDb->activateDeepLinkExport)
            {
                throw new \Exception('You are not allowed to use this service.');
            }

            $strTable = $objDb->export_table;
            $arrSelectedFields = StringUtil::deserialize($objDb->fields, true);
            // Replace insert tags
            $filterExpression = Controller::replaceInsertTags(trim($objDb->filterExpression));
            $exportType = $objDb->exportType;
            $arrayDelimiter = $objDb->arrayDelimiter;

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
        else
        {
            throw new \Exception('You are not allowed to use this service.');
        }

        $options = [
            'strSorting'          => $sortingExpression,
            'exportType'          => $exportType,
            'strDelimiter'        => ';',
            'strEnclosure'        => '"',
            'arrFilter'           => $filterExpression != '' ? json_decode($filterExpression) : [],
            'strDestination'      => null,
            'arrSelectedFields'   => $arrSelectedFields,
            'useLabelForHeadline' => null,
            'arrayDelimiter'      => $arrayDelimiter,
        ];

        // Call Export class
        self::exportTable($strTable, $options);
    }

    /**
     * @param $strTable
     * @param array $options
     * @throws \Exception
     */
    public static function exportTable($strTable, array $options = [])
    {
        // Defaults
        $preDefinedOptions = [
            'strSorting'          => 'id ASC',
            // Export Type csv or xml
            'exportType'          => 'csv',
            'strDelimiter'        => ';',
            'strEnclosure'        => '"',
            // arrFilter array(array("published=?",1),array("pid=6",1))
            'arrFilter'           => [],
            // strDestination f.ex: files/mydir
            'strDestination'      => null,
            // arrSelectedFields f.ex: array('firstname', 'lastname', 'street')
            'arrSelectedFields'   => null,
            // useLabelForHeadline: can be null or en, de, fr, ...
            'useLabelForHeadline' => null,
            // arrayDelimiter f.ex: ||
            'arrayDelimiter'      => '||',
        ];
        $options = array_merge($preDefinedOptions, $options);
        $strSorting = $options['strSorting'];
        $exportType = $options['exportType'];
        $strDelimiter = $options['strDelimiter'];
        $strEnclosure = $options['strEnclosure'];
        $arrFilter = $options['arrFilter'];
        $strDestination = $options['strDestination'];
        $arrSelectedFields = $options['arrSelectedFields'];
        $useLabelForHeadline = $options['useLabelForHeadline'];
        $arrayDelimiter = $options['arrayDelimiter'];

        $arrData = [];

        // Load Datacontainer
        if (!is_array($GLOBALS['TL_DCA'][$strTable]))
        {
            Controller::loadDataContainer($strTable, true);
        }

        $dca = [];
        if (is_array($GLOBALS['TL_DCA'][$strTable]))
        {
            $dca = $GLOBALS['TL_DCA'][$strTable];
        }

        // If no fields are selected, then list the whole table
        if ($arrSelectedFields === null || empty($arrSelectedFields))
        {
            $arrSelectedFields = Database::getInstance()->getFieldNames($strTable);
        }

        // create headline
        if ($useLabelForHeadline !== null)
        {
            // Use language file
            Controller::loadLanguageFile($strTable, $useLabelForHeadline);
        }

        $arrHeadline = [];
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
            $arrFilter = [];
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

        $objDb = Database::getInstance()->prepare("SELECT * FROM  " . $strTable . " WHERE " . implode(' AND ', $arrProcedures) . " ORDER BY " . $strSorting)->execute($arrValues);

        while ($dataRecord = $objDb->fetchAssoc())
        {
            $arrRow = [];
            foreach ($arrSelectedFields as $field)
            {
                $value = '';

                // Handle arrays correctly
                if ($dataRecord[$field] != '')
                {
                    // Replace newlines with [NEWLINE]
                    if ($GLOBALS['TL_DCA'][$strTable]['fields'][$field]['inputType'] === 'textarea')
                    {
                        $value = $dataRecord[$field];
                        $dataRecord[$field] = str_replace(PHP_EOL, '[NEWLINE]', $value);
                    }

                    if ($GLOBALS['TL_DCA'][$strTable]['fields'][$field]['csv'] != '')
                    {
                        $delim = $GLOBALS['TL_DCA'][$strTable]['fields'][$field]['csv'];
                        $value = implode($delim, StringUtil::deserialize($dataRecord[$field], true));
                    }
                    elseif ($GLOBALS['TL_DCA'][$strTable]['fields'][$field]['eval']['multiple'] === true)
                    {
                        $value = implode($arrayDelimiter, StringUtil::deserialize($dataRecord[$field], true));
                    }
                    else
                    {
                        $value = $dataRecord[$field];
                    }
                }

                // HOOK: add custom value
                if (isset($GLOBALS['TL_HOOKS']['exportTable']) && is_array($GLOBALS['TL_HOOKS']['exportTable']))
                {
                    foreach ($GLOBALS['TL_HOOKS']['exportTable'] as $callback)
                    {
                        $objCallback = System::importStatic($callback[0]);
                        $value = $objCallback->{$callback[1]}($field, $value, $strTable, $dataRecord, $dca, $options);
                    }
                }

                $arrRow[] = $value;
            }
            $arrData[] = $arrRow;
        }

        // xml-output
        if ($exportType === 'xml')
        {
            $objXml = new \XMLWriter();
            $objXml->openMemory();
            $objXml->setIndent(true);
            $objXml->setIndentString("\t");
            $objXml->startDocument('1.0', 'UTF-8');

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

                foreach ($arrRow as $i => $fieldvalue)
                {
                    // New field
                    $objXml->startElement($arrHeadline[$i]);

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
                new Folder($strDestination);
                $objFolder = FilesModel::findByPath($strDestination);
                if ($objFolder !== null)
                {
                    if ($objFolder->type == 'folder' && is_dir(TL_ROOT . '/' . $objFolder->path))
                    {
                        $objFile = new File($objFolder->path . '/' . $strTable . '_' . Date::parse('Y-m-d_H-i-s') . '.xml', false);
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
                    $objFile = new File($strDestination . '/' . $strTable . '_' . Date::parse('Y-m-d_H-i-s') . '.csv');
                    $objFile->write('');
                    // Convert special chars
                    $arrFinal = [];
                    foreach ($arrData as $arrRow)
                    {
                        $arrLine = array_map(function ($v) {
                            return html_entity_decode(htmlspecialchars_decode($v));
                        }, $arrRow);
                        $arrFinal[] = $arrLine;
                    }

                    // Load the CSV document from a string
                    $csv = Writer::createFromString('');
                    $csv->setOutputBOM(Reader::BOM_UTF8);

                    $csv->setDelimiter($strDelimiter);
                    $csv->setEnclosure($strEnclosure);

                    // Insert all the records
                    $csv->insertAll($arrFinal);

                    // Write csv into file
                    $objFile->write($csv);
                    $objFile->close();
                }
                return;
            }

            // Convert special chars
            $arrFinal = [];
            foreach ($arrData as $arrRow)
            {
                $arrLine = array_map(function ($v) {
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

            $csv->setDelimiter($strDelimiter);
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
        $objDb = Database::getInstance();
        $arrFields = $objDb->listFields($strTable);
        $arrNew = [];
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
        $new = [];
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
        if (Validator::isBinaryUuid($value))
        {
            return StringUtil::binToUuid($value);
        }
        return $value;
    }

}
