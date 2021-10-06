<?php

declare(strict_types=1);

/*
 * This file is part of Export Table for Contao CMS.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Config;

use http\Exception\InvalidArgumentException;

class Config
{
    private $strTable = 'tl_member';
    private $strExportType = 'csv';
    private $strSortBy = 'id';
    private $strSortDirection = 'asc';
    private $strDelimiter = ';';
    private $strEnclosure = '"';
    private $arrFilter = [];
    private $strTargetFolder;
    private $strTempFolder = 'system/tmp';
    private $arrFields = [];
    private $strHeadlineLabelLang;
    private $strArrayDelimiter = '||';
    private $strFilename;
    private $activateDeepLinkExport = false;
    private $deepLinkExportKey = '';
    private $invalidFilterExpr = [
        'delete',
        'drop',
        'update',
        'alter',
        'truncate',
        'insert',
        'create',
        'clone',
    ];

    public function getTable(): ?string
    {
        return $this->strTable;
    }

    public function setTable(string $strTable): self
    {
        $this->strTable = $strTable;

        return $this;
    }

    public function getExportType(): ?string
    {
        return $this->strExportType;
    }

    public function setExportType(string $strExportType): self
    {
        $this->strExportType = $strExportType;

        return $this;
    }

    public function getSortBy(): string
    {
        return $this->strSortBy;
    }

    public function setSortBy(string $strSortBy): self
    {
        $this->strSortBy = $strSortBy;

        return $this;
    }

    public function getSortDirection(): string
    {
        return $this->strSortDirection;
    }

    public function setSortDirection(string $strSortDirection): self
    {
        $this->strSortDirection = $strSortDirection;

        return $this;
    }

    public function getDelimiter(): string
    {
        return $this->strDelimiter;
    }

    public function setDelimiter(string $strDelimiter = ';'): self
    {
        $this->strDelimiter = $strDelimiter;

        return $this;
    }

    public function getEnclosure(): string
    {
        return $this->strEnclosure;
    }

    public function setEnclosure(string $strEnclosure = '"'): self
    {
        $this->strEnclosure = $strEnclosure;

        return $this;
    }

    public function getArrayDelimiter(): string
    {
        return $this->strArrayDelimiter;
    }

    public function setArrayDelimiter(string $strArrayDelimiter): self
    {
        $this->strArrayDelimiter = $strArrayDelimiter;

        return $this;
    }

    public function getFilter(): array
    {
        return $this->arrFilter;
    }

    public function setFilter(string $jsonArrFilter = ''): self
    {
        $arrFilter = json_decode($jsonArrFilter);

        if (!\is_array($arrFilter)) {
            throw new InvalidArgumentException('Wrong argument for $jsonArrFilter. Please use a json encoded array e.g. [["city=?"],["New York"]]');
        }
        $this->arrFilter = $arrFilter;

        return $this;
    }

    public function getInvalidFilterExpr(): array
    {
        return $this->invalidFilterExpr;
    }

    public function setInvalidFilterExpr(array $invalidFilterExpr = []): self
    {
        $this->invalidFilterExpr = $invalidFilterExpr;

        return $this;
    }

    public function getTargetFolder(): ?string
    {
        return $this->strTargetFolder;
    }

    public function setTargetFolder(string $strTargetFolder): self
    {
        $this->strTargetFolder = $strTargetFolder;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->strFilename;
    }

    public function setFilename(string $strFilename): self
    {
        $this->strFilename = $strFilename;

        return $this;
    }

    public function getTempFolder(): string
    {
        return $this->strTempFolder;
    }

    public function setTempFolder(string $strTempFolder): self
    {
        $this->strTempFolder = $strTempFolder;

        return $this;
    }

    public function getFields(): array
    {
        return $this->arrFields;
    }

    public function setFields(array $arrFields): self
    {
        $this->arrFields = $arrFields;

        return $this;
    }

    public function getHeadlineLabelLang(): ?string
    {
        return $this->strHeadlineLabelLang;
    }

    public function setHeadlineLabelLang(string $strHeadlineLabelLang): self
    {
        $this->strHeadlineLabelLang = $strHeadlineLabelLang;

        return $this;
    }

    public function getActivateDeepLinkExport(): bool
    {
        return $this->activateDeepLinkExport;
    }

    public function setActivateDeepLinkExport(bool $blnActivate): self
    {
        $this->activateDeepLinkExport = $blnActivate;

        return $this;
    }

    public function getDeepLinkExportKey(): string
    {
        return $this->deepLinkExportKey;
    }

    public function setDeepLinkExportKey(string $token): self
    {
        $this->deepLinkExportKey = $token;

        return $this;
    }
}
