<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Config;

use Contao\CoreBundle\Framework\ContaoFramework;
use Markocupic\ExportTable\Model\ExportTableModel;

class GetConfigFromToken
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly GetConfigFromModel $getConfigFromModel,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function get(string $strToken): Config
    {
        if (!$this->isValidKey($strToken)) {
            throw new \Exception('You are not allowed to use this service.');
        }

        return $this->getConfigFromModel->get($this->getExportFromToken($strToken));
    }

    private function isValidKey(string $strToken): bool
    {
        $exportTableModelAdapter = $this->framework->getAdapter(ExportTableModel::class);

        if (null !== ($objExport = $exportTableModelAdapter->findOneByToken($strToken))) {
            if ($objExport->activateDeepLinkExport) {
                return true;
            }
        }

        return false;
    }

    private function getExportFromToken(string $strToken): ExportTableModel|null
    {
        $exportTableModelAdapter = $this->framework->getAdapter(ExportTableModel::class);

        if (null !== ($objExport = $exportTableModelAdapter->findOneByToken($strToken))) {
            if ($objExport->activateDeepLinkExport) {
                return $objExport;
            }
        }

        return null;
    }
}
