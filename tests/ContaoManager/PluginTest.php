<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Tests\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\DelegatingParser;
use Markocupic\ExportTable\ContaoManager\Plugin;
use Markocupic\ExportTable\MarkocupicExportTable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\Route;

class PluginTest extends TestCase
{
    public function testReturnsTheBundles(): void
    {
        $bundles = (new Plugin())->getBundles(new DelegatingParser());

        $this->assertCount(1, $bundles);
        $this->assertInstanceOf(BundleConfig::class, $bundles[0]);
        $this->assertSame(MarkocupicExportTable::class, $bundles[0]->getName());
        $this->assertSame([ContaoCoreBundle::class], $bundles[0]->getLoadAfter());
    }

    public function testLoadsTheDownloadRoute(): void
    {
        $classLoader = new class() extends AttributeClassLoader {
            protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, object $attr): void
            {
                $route->setDefault('_controller', $class->getName());
            }
        };

        $resolver = new LoaderResolver([new AttributeDirectoryLoader(new FileLocator(), $classLoader)]);
        $routes = (new Plugin())->getRouteCollection($resolver, $this->createStub(KernelInterface::class));

        $route = $routes->get('export_table_download_table');

        $this->assertNotNull($route);
        $this->assertSame('/_export_table_download_table', $route->getPath());
        $this->assertSame('frontend', $route->getDefault('_scope'));
    }
}
