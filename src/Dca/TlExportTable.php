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
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Markocupic\ExportTable\Config\GetConfigFromModel;
use Markocupic\ExportTable\Export\ExportTable;
use Markocupic\ExportTable\Model\ExportTableModel;
use Symfony\Component\HttpFoundation\RequestStack;

class TlExportTable extends Backend
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

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
    public function runExport(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->request->has('exportTableBtn') && 'tl_export_table' === $request->request->get('FORM_SUBMIT')) {
            $request->request->remove('exportTableBtn');

            if (null !== ($model = ExportTableModel::findByPk(Input::get('id')))) {
                /** @var GetConfigFromModel $config */
                $objConfig = System::getContainer()->get(GetConfigFromModel::class);

                /** @var ExportTable$objExport */
                $objExport = System::getContainer()->get(ExportTable::class);

                $objExport->run($objConfig->get($model));
                exit();
            }
        }
    }

    /**
     * @Callback(table="tl_export_table", target="fields.table.options")
     */
    public function optionsCbGetTables(): array
    {
        $objTables = Database::getInstance()
            ->listTables()
        ;

        $arrOptions = [];

        foreach ($objTables as $table) {
            $arrOptions[] = $table;
        }

        return $arrOptions;
    }

    /**
     * @Callback(table="tl_export_table", target="edit.buttons")
     */
    public function buttonsCallback($arrButtons, DC_Table $dc): array
    {
        if ('edit' === Input::get('act')) {
            $save = $arrButtons['save'];
            $exportTable = '<button type="submit" name="exportTableBtn" id="exportTableBtn" class="tl_submit" accesskey="n">'.$GLOBALS['TL_LANG']['tl_export_table']['launchExportButton'].'</button>';
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
    public function optionsCbSelectedFields(DataContainer $dc): array
    {
        return $this->getFieldsFromTable($dc->activeRecord->table);
    }

    /**
     * @Callback(table="tl_export_table", target="fields.deepLinkInfo.input_field")
     */
    public function generateDeepLinkInfo(): string
    {
        $objDb = Database::getInstance()
            ->prepare('SELECT * FROM tl_export_table WHERE id=? LIMIT 0,1')
            ->execute(
                Input::get('id')
            )
        ;
        $key = $objDb->token;
        $href = sprintf(
            '%s/_export_table_download_table?action=exportTable&amp;key=%s',
            Environment::get('url'),
            $key
        );

        return '
<div class="clr widget deep_link_info">
<br><br>
<table cellpadding="0" cellspacing="0" width="100%" summary="">
	<tr class="odd">
		<td><h2>'.$GLOBALS['TL_LANG']['tl_export_table']['deepLinkInfoText'].'</h2></td>
    </tr>
	<tr class="even">
		<td><a href="'.$href.'">'.$href.'</a></td>
	</tr>
</table>
</div>
				';
    }

    private function getFieldsFromTable(string $strTable = ''): array
    {
        if ('' === $strTable) {
            return [];
        }

        $objFields = Database::getInstance()
            ->listFields($strTable, 1)
        ;

        $arrOptions = [];

        foreach ($objFields as $field) {
            if (\in_array($field['name'], $arrOptions, true)) {
                continue;
            }

            if ('PRIMARY' === $field['name']) {
                continue;
            }
            $arrOptions[$field['name']] = $field['name'].' ['.$field['type'].']';
        }

        return $arrOptions;
    }
}
