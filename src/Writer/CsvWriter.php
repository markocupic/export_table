<?php

namespace Markocupic\ExportTable\Writer;


use Contao\CoreBundle\Exception\ResponseException;
use Contao\File;
use Haste\IO\Reader\ArrayReader;
use Haste\IO\Writer\CsvFileWriter;
use Markocupic\ExportTable\Config\Config;
use Symfony\Component\HttpFoundation\Response;

class CsvWriter implements WriterInterface{

    /**
     * @param array $arrData
     * @param Config $config
     * @throws \Exception
     */
    public function write(array $arrData, Config $config)
    {
        $targetFolder = $config->getTargetFolder() ?? $config->getTempFolder();

        // Get reader from array
        $objReader = new ArrayReader($arrData);

        $strFilename = $config->getFilename() ?? $config->getTable().'.csv';
        $targetFolder = $config->getTargetFolder() ?? $config->getTempFolder();

        $objWriter = new CsvFileWriter($targetFolder.'/'.$strFilename);
        $objWriter->setDelimiter($config->getDelimiter());
        $objWriter->setEnclosure($config->getEnclosure());

        // Create a file
        $objWriter->writeFrom($objReader);

        // Download the file
        $objFile = new File($objWriter->getFilename());

        $response = new Response($objFile->sendToBrowser());
        return new ResponseException($response);
    }

}