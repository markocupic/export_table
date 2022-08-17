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

namespace Markocupic\ExportTable\Config;

use Markocupic\ExportTable\Model\ExportTableModel;

class Config
{
    private array $arrData = [
        'model' => null,
        'title' => '',
        'table' => null,
        'exportType' => 'csv',
        'sortBy' => 'id',
        'sortDirection' => 'ASC',
        'delimiter' => ';',
        'enclosure' => '"',
        'filter' => [],
        'arrayDelimiter' => '||',
        'bom' => '',
        'sendFileToTheBrowser' => true,
        'tempFolder' => 'system/tmp',
        'fields' => [],
        'addHeadline' => true,
        'headlineFields' => [],
        'saveExport' => false,
        'saveExportDirectory' => null,
        'overrideFile' => false,
        'filename' => null,
        'activateDeepLinkExport' => false,
        'token' => null,
        'rowCallback' => null,
        'notAllowedFilterExpr' => [
            'delete',
            'drop',
            'update',
            'alter',
            'truncate',
            'insert',
            'create',
            'clone',
        ],
    ];

    public function __construct(string $strTable = 'tl_member')
    {
        $this->arrData['table'] = $strTable;
    }

    public function getModel(): ExportTableModel|null
    {
        return $this->arrData['model'];
    }

    /**
     * @return $this
     */
    public function setModel(ExportTableModel $model): self
    {
        $this->arrData['model'] = $model;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->arrData['title'];
    }

    /**
     * @return $this
     */
    public function setTitle(string $strTitle = ''): self
    {
        $this->arrData['title'] = $strTitle;

        return $this;
    }

    public function getTable(): string
    {
        return $this->arrData['table'];
    }

    /**
     * @return $this
     */
    public function setTable(string $strTable): self
    {
        $this->arrData['table'] = $strTable;

        return $this;
    }

    public function getExportType(): string|null
    {
        return $this->arrData['exportType'];
    }

    /**
     * @return $this
     */
    public function setExportType(string $strExportType): self
    {
        $this->arrData['exportType'] = $strExportType;

        return $this;
    }

    public function getSortBy(): string
    {
        return empty($this->arrData['sortBy']) ? 'id' : $this->arrData['sortBy'];
    }

    /**
     * @return $this
     */
    public function setSortBy(string $strSortBy = 'id'): self
    {
        $this->arrData['sortBy'] = $strSortBy;

        return $this;
    }

    public function getSortDirection(): string
    {
        return empty($this->arrData['sortDirection']) ? 'ASC' : $this->arrData['sortDirection'];
    }

    /**
     * @throws \Exception
     *
     * @return $this
     */
    public function setSortDirection(string $strSortDirection = 'ASC'): self
    {
        $strSortDirection = strtoupper($strSortDirection);

        if (!\in_array($strSortDirection, ['ASC', 'DESC'], true)) {
            throw new \Exception(sprintf('Sort direction should be "ASC" or "DESC", %s given.', $strSortDirection));
        }
        $this->arrData['sortDirection'] = $strSortDirection;

        return $this;
    }

    public function getDelimiter(): string
    {
        return $this->arrData['delimiter'];
    }

    /**
     * @return $this
     */
    public function setDelimiter(string $strDelimiter = ';'): self
    {
        $this->arrData['delimiter'] = $strDelimiter;

        return $this;
    }

    public function getOutputBom(): string
    {
        return $this->arrData['bom'];
    }

    /**
     * @return $this
     */
    public function setOutputBom(string $strBom = ''): self
    {
        $this->arrData['bom'] = $strBom;

        return $this;
    }

    public function getEnclosure(): string
    {
        return $this->arrData['enclosure'];
    }

    /**
     * @return $this
     */
    public function setEnclosure(string $strEnclosure = '"'): self
    {
        $this->arrData['enclosure'] = $strEnclosure;

        return $this;
    }

    public function getArrayDelimiter(): string
    {
        return $this->arrData['arrayDelimiter'];
    }

    /**
     * @return $this
     */
    public function setArrayDelimiter(string $strArrayDelimiter): self
    {
        $this->arrData['arrayDelimiter'] = $strArrayDelimiter;

        return $this;
    }

    public function getSendFileToTheBrowser(): bool
    {
        return $this->arrData['sendFileToTheBrowser'];
    }

    /**
     * @return $this
     */
    public function setSendFileToTheBrowser(bool $blnSend): self
    {
        $this->arrData['sendFileToTheBrowser'] = $blnSend;

        return $this;
    }

    public function getFilter(): array
    {
        return $this->arrData['filter'];
    }

    /**
     * @return $this
     */
    public function setFilter(array $arrFilter): self
    {
        $this->arrData['filter'] = $arrFilter;

        return $this;
    }

    public function getNotAllowedFilterExpr(): array
    {
        return $this->arrData['notAllowedFilterExpr'];
    }

    /**
     * @return $this
     */
    public function setNotAllowedFilterExpr(array $notAllowedFilterExpr = []): self
    {
        $this->arrData['notAllowedFilterExpr'] = $notAllowedFilterExpr;

        return $this;
    }

    public function getFilename(): string|null
    {
        return $this->arrData['filename'];
    }

    public function getSaveExport(): bool
    {
        return $this->arrData['saveExport'];
    }

    /**
     * @return $this
     */
    public function setSaveExport(bool $saveExport): self
    {
        $this->arrData['saveExport'] = $saveExport;

        return $this;
    }

    public function getOverrideFile(): bool
    {
        return $this->arrData['overrideFile'];
    }

    /**
     * @return $this
     */
    public function setOverrideFile(bool $overrideFile): self
    {
        $this->arrData['overrideFile'] = $overrideFile;

        return $this;
    }

    public function getSaveExportDirectory(): string|null
    {
        return $this->arrData['saveExportDirectory'];
    }

    /**
     * @return $this
     */
    public function setSaveExportDirectory(string|null $stringUuid): self
    {
        $this->arrData['saveExportDirectory'] = $stringUuid;

        return $this;
    }

    /**
     * @return $this
     */
    public function setFilename(string $strFilename): self
    {
        $this->arrData['filename'] = $strFilename;

        return $this;
    }

    public function getTempFolder(): string
    {
        return $this->arrData['tempFolder'];
    }

    /**
     * @return $this
     */
    public function setTempFolder(string $strTempFolder): self
    {
        $this->arrData['tempFolder'] = $strTempFolder;

        return $this;
    }

    public function getFields(): array
    {
        return $this->arrData['fields'];
    }

    /**
     * @return $this
     */
    public function setFields(array $arrFields): self
    {
        $this->arrData['fields'] = $arrFields;

        return $this;
    }

    public function getAddHeadline(): bool
    {
        return $this->arrData['addHeadline'];
    }

    /**
     * @return $this
     */
    public function setAddHeadline(bool $blnAddHeadline): self
    {
        $this->arrData['addHeadline'] = $blnAddHeadline;

        return $this;
    }

    public function getHeadlineFields(): array
    {
        return $this->arrData['headlineFields'];
    }

    /**
     * @return $this
     */
    public function setHeadlineFields(array $arrHeadlineFields): self
    {
        $this->arrData['headlineFields'] = $arrHeadlineFields;

        return $this;
    }

    public function isActivateDeepLinkExport(): bool
    {
        return $this->arrData['activateDeepLinkExport'];
    }

    /**
     * @return $this
     */
    public function setActivateDeepLinkExport(bool $blnActivate): self
    {
        $this->arrData['activateDeepLinkExport'] = $blnActivate;

        return $this;
    }

    public function getToken(): string
    {
        return $this->arrData['token'];
    }

    /**
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->arrData['token'] = $token;

        return $this;
    }

    public function getRowCallback(): callable|null
    {
        return $this->arrData['rowCallback'];
    }

    /**
     * @return $this
     */
    public function setRowCallback(callable|null $callback): self
    {
        $this->arrData['rowCallback'] = $callback;

        return $this;
    }

    public function getAll(): array
    {
        return $this->arrData;
    }
}
