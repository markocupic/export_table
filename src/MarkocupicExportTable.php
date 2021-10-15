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

namespace Markocupic\ExportTable;

use Markocupic\ExportTable\DependencyInjection\Compiler\WriterResourcePass;
use Markocupic\ExportTable\DependencyInjection\MarkocupicExportTableExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MarkocupicExportTable extends Bundle
{
    public function getContainerExtension(): MarkocupicExportTableExtension
    {
        return new MarkocupicExportTableExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WriterResourcePass());
    }
}
