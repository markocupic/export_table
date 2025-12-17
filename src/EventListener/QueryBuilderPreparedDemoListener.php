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

namespace Markocupic\ExportTable\EventListener;

use Doctrine\DBAL\Connection;
use Markocupic\ExportTable\Event\QueryBuilderPreparedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class QueryBuilderPreparedDemoListener
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * For Demo purposes only.
     */
    public function __invoke(QueryBuilderPreparedEvent $event): void
    {
        $model = $event->getConfig()->getModel();

        if (null === $model || 'tl_member demo export' !== $model->title) {
            return;
        }

        // Get the query builder object
        $qb = $event->getQueryBuilder();

        // Add more conditions to the query
        $qb->andWhere('t.gender = :gender')->setParameter('gender', 'female');

        $event->setQueryBuilder($qb);
    }
}
