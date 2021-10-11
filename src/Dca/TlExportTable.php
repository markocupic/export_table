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

namespace Markocupic\ExportTable\Dca;

use Contao\Backend;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Environment;
use Markocupic\ExportTable\Config\GetConfigFromModel;
use Markocupic\ExportTable\Export\ExportTable;
use Markocupic\ExportTable\Helper\DatabaseHelper;
use Markocupic\ExportTable\Model\ExportTableModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as Twig;

class TlExportTable extends Backend
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var DatabaseHelper
     */
    private $databaseHelper;

    /**
     * @var GetConfigFromModel
     */
    private $getConfigFromModel;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var ExportTable
     */
    private $exportTable;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ContaoFramework $framework, RequestStack $requestStack, DatabaseHelper $databaseHelper, GetConfigFromModel $getConfigFromModel, ExportTable $exportTable, Twig $twig, TranslatorInterface $translator)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
        $this->databaseHelper = $databaseHelper;
        $this->getConfigFromModel = $getConfigFromModel;
        $this->exportTable = $exportTable;
        $this->twig = $twig;
        $this->translator = $translator;

        parent::__construct();
    }

    /**
     * @Callback(table="tl_export_table", target="config.onload")
     */
    public function setPost(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->request->has('exportTableBtn') && 'tl_export_table' === $request->request->get('FORM_SUBMIT')) {
            $request->request->set('save', true);
        }
    }

    /**
     * @Callback(table="tl_export_table", target="config.onsubmit")
     */
    public function runExport(DataContainer $dc)
    {
        if (!$dc->activeRecord->id || '' === $dc->activeRecord->id) {
            return;
        }

        $pk = $dc->activeRecord->id;

        $request = $this->requestStack->getCurrentRequest();

        if ($request->request->has('exportTableBtn') && 'tl_export_table' === $request->request->get('FORM_SUBMIT')) {
            $request->request->remove('exportTableBtn');

            $exportTableModelAdapter = $this->framework->getAdapter(ExportTableModel::class);

            if (null !== ($model = $exportTableModelAdapter->findByPk($pk))) {
                $response = new Response($this->exportTable->run($this->getConfigFromModel->get($model)));

                return new ResponseException($response);
            }
        }
    }

    /**
     * @Callback(table="tl_export_table", target="fields.table.options")
     */
    public function listTableNames(): array
    {
        $databaseAdapter = $this->framework->getAdapter(Database::class);
        $arrTableNames = $databaseAdapter->getInstance()->listTables();

        return \is_array($arrTableNames) ? $arrTableNames : [];
    }

    /**
     * @Callback(table="tl_export_table", target="edit.buttons")
     */
    public function insertExportButton($arrButtons, DC_Table $dc): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if ('edit' === $request->query->get('act')) {
            $save = $arrButtons['save'];
            $exportTable = sprintf(
                '<button type="submit" name="exportTableBtn" id="exportTableBtn" class="tl_submit" accesskey="n">%s</button>',
                $this->translator->trans('tl_export_table.launchExportButton', [], 'contao_default')
            );

            $saveNclose = $arrButtons['saveNclose'];

            unset($arrButtons);

            // Set correct order
            $arrButtons = [
                'save' => $save,
                'exportTable' => $exportTable,
                'saveNclose' => $saveNclose,
            ];
        }

        return $arrButtons;
    }

    /**
     * @Callback(table="tl_export_table", target="fields.fields.options")
     * @Callback(table="tl_export_table", target="fields.sortBy.options")
     */
    public function listFields(DataContainer $dc): array
    {
        if ($dc->activeRecord->table && '' === ($strTable = $dc->activeRecord->table)) {
            return [];
        }

        return $this->databaseHelper->listFields($strTable, true, true);
    }

    /**
     * @Callback(table="tl_export_table", target="fields.deepLinkInfo.input_field")
     */
    public function generateDeepLinkInfo(DataContainer $dc): string
    {
        $exportTableModel = $this->framework->getAdapter(ExportTableModel::class);
        $environmentAdapter = $this->framework->getAdapter(Environment::class);

        if (null === ($objModel = $exportTableModel->findByPk($dc->activeRecord->id))) {
            return '';
        }

        $link = sprintf(
            '%s/_export_table_download_table?action=exportTable&amp;key=%s',
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
