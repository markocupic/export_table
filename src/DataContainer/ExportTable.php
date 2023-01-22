<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Database;
use Contao\DataContainer;
use Contao\Environment;
use Markocupic\ExportTable\Config\GetConfigFromModel;
use Markocupic\ExportTable\Export\ExportTable as ExportTableService;
use Markocupic\ExportTable\Helper\DatabaseHelper;
use Markocupic\ExportTable\Model\ExportTableModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ExportTable
{
    private array $writerAliases = [];

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly RequestStack $requestStack,
        private readonly ScopeMatcher $scopeMatcher,
        private readonly DatabaseHelper $databaseHelper,
        private readonly GetConfigFromModel $getConfigFromModel,
        private readonly ExportTableService $exportTable,
        private readonly Twig $twig,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function addWriterAlias(string $alias): void
    {
        $this->writerAliases[$alias] = $alias;
    }

    #[AsCallback(table: 'tl_export_table', target: 'config.onload')]
    public function setPalettes(DataContainer $dc): void
    {
        $exportTableModelAdapter = $this->framework->getAdapter(ExportTableModel::class);

        if (null !== ($model = $exportTableModelAdapter->findByPk($dc->id))) {
            $arrPalette = &$GLOBALS['TL_DCA']['tl_export_table']['palettes'];

            if ($arrPalette[$model->exportType]) {
                $arrPalette['default'] = $arrPalette[$model->exportType];
            }
        }
    }

    /**
     * Run export.
     */
    #[AsCallback(table: 'tl_export_table', target: 'config.onload')]
    public function runExport(): void
    {
        $exportTableModelAdapter = $this->framework->getAdapter(ExportTableModel::class);
        $controllerAdapter = $this->framework->getAdapter(Controller::class);

        $request = $this->requestStack->getCurrentRequest();
        $pk = $request->query->get('id');

        if ('export' === $request->query->get('action') && $pk) {
            if (null !== ($model = $exportTableModelAdapter->findByPk($pk))) {
                $this->exportTable->run($this->getConfigFromModel->get($model));
            }

            $request = $this->requestStack->getCurrentRequest();

            if ($request && $this->scopeMatcher->isBackendRequest($request)) {
                $request->query->remove('id');
                $request->query->remove('action');
                $request->overrideGlobals();

                $controllerAdapter->redirect($request->getUri());
            }
        }
    }

    #[AsCallback(table: 'tl_export_table', target: 'fields.table.options')]
    public function listTableNames(): array
    {
        $databaseAdapter = $this->framework->getAdapter(Database::class);
        $arrTableNames = $databaseAdapter->getInstance()->listTables();

        return \is_array($arrTableNames) ? $arrTableNames : [];
    }

    #[AsCallback(table: 'tl_export_table', target: 'fields.exportType.options')]
    public function listWriters(): array
    {
        return $this->writerAliases;
    }

    #[AsCallback(table: 'tl_export_table', target: 'fields.fields.options')]
    #[AsCallback(table: 'tl_export_table', target: 'fields.sortBy.options')]
    public function listFields(DataContainer $dc): array
    {
        $strTable = $dc->activeRecord->table;

        $databaseAdapter = $this->framework->getAdapter(Database::class);

        if (!$strTable || !$databaseAdapter->getInstance()->tableExists($strTable)) {
            return [];
        }

        return $this->databaseHelper->listFields($strTable, true, true);
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[AsCallback(table: 'tl_export_table', target: 'fields.deepLinkInfo.input_field')]
    public function generateDeepLinkInfo(DataContainer $dc): string
    {
        $exportTableModel = $this->framework->getAdapter(ExportTableModel::class);
        $environmentAdapter = $this->framework->getAdapter(Environment::class);

        if (null === ($objModel = $exportTableModel->findByPk($dc->activeRecord->id))) {
            return '';
        }

        $link = sprintf(
            '%s/_export_table_download_table?action=exportTable&key=%s',
            $environmentAdapter->get('url'),
            $objModel->token
        );

        return $this->twig->render(
            '@MarkocupicExportTable/backend/deep_link_info.html.twig',
            [
                'info_text' => $this->translator->trans('tl_export_table.deepLinkInfoText', [], 'contao_default'),
                'link' => $link,
            ]
        );
    }
}
