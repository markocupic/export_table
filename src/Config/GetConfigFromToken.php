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

use Contao\CoreBundle\Framework\ContaoFramework;
use Markocupic\ExportTable\Model\ExportTableModel;

class GetConfigFromToken
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var GetConfigFromModel
     */
    private $getConfigFromModel;

    public function __construct(ContaoFramework $framework, GetConfigFromModel $getConfigFromModel)
    {
        $this->framework = $framework;
        $this->getConfigFromModel = $getConfigFromModel;
    }

    public function get(string $strToken): Config
    {
        if (!$this->isValidKey($strToken)) {
            throw new \Exception('You are not allowed to use this service.');
        }

        return $this->getConfigFromModel->get($this->getExportByKey($strToken));
    }

    private function isValidKey(string $strToken): bool
    {
        $exportTableModelAdapter = $this->framework->getAdapter(ExportTableModel::class);

        if (null !== ($objExport = $exportTableModelAdapter->findOneByDeepLinkExportKey($strToken))) {
            if ($objExport->activateDeepLinkExport) {
                return true;
            }
        }

        return false;
    }

    private function getExportByKey(string $strToken): ?ExportTableModel
    {
        $exportTableModelAdapter = $this->framework->getAdapter(ExportTableModel::class);

        if (null !== ($objExport = $exportTableModelAdapter->findOneByDeepLinkExportKey($strToken))) {
            if ($objExport->activateDeepLinkExport) {
                return $objExport;
            }
        }

        return null;
    }
}
