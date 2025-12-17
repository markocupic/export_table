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

namespace Markocupic\ExportTable\Event;

use Doctrine\DBAL\Query\QueryBuilder;
use Markocupic\ExportTable\Config\Config;
use Symfony\Contracts\EventDispatcher\Event;

class QueryBuilderPreparedEvent extends Event
{
    public function __construct(
        private QueryBuilder $queryBuilder,
        private readonly Config $config,
    ) {
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
