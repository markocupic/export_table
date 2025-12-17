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

namespace Markocupic\ExportTable\Helper;

use Contao\CoreBundle\InsertTag\InsertTagParser;
use Doctrine\DBAL\Query\QueryBuilder;
use Markocupic\ExportTable\Config\Config;

class WhereExpressionParser
{
    public function __construct(
        private readonly InsertTagParser $insertTagParser,
        private readonly StringHelper $stringHelper,
    ) {
    }

    public function withWhereStmt(QueryBuilder $qb, Config $objConfig): QueryBuilder
    {
        $whereData = $this->parseAndProcessFilter($objConfig->getFilter());

        if (!$whereData['hasValidFilter']) {
            return $qb;
        }

        $this->validateFilterExpression($whereData['statements'], $whereData['params'], $objConfig);

        foreach ($whereData['statements'] as $statement) {
            $qb->andWhere($statement);
        }

        $qb->setParameters($whereData['params']);

        return $qb;
    }

    private function parseAndProcessFilter(array $arrFilter): array
    {
        $decodedFilter = $this->replaceInsertTags($arrFilter);

        if (!$this->isValidFilterStructure($decodedFilter)) {
            return ['hasValidFilter' => false];
        }

        $whereStatements = $this->extractFilterStatements($decodedFilter[0]);
        $whereParams = $this->extractFilterParameters($decodedFilter[1]);

        return [
            'hasValidFilter' => !empty(array_filter($whereStatements)),
            'statements' => $whereStatements,
            'params' => $whereParams,
        ];
    }

    private function replaceInsertTags(array $arrFilter): array
    {
        // The filter expression has to be entered as a JSON encoded array
        // -> [["tableName.field=? OR tableName.field=?"],["valueA","valueB"]] or
        // -> [["tableName.field=?", "tableName.field=?"],["valueA","valueB"]]
        $strFilter = json_encode($arrFilter);
        // Replace insert tags: Replace {{GET::key}} with a given value of a corresponding $_GET parameter.
        $strFilter = $this->insertTagParser->replaceInline($strFilter);

        return json_decode($strFilter, true);
    }

    private function isValidFilterStructure(array $decodedFilter): bool
    {
        return 2 === \count($decodedFilter);
    }

    private function extractFilterStatements(array|string $statementsData): array
    {
        if (\is_array($statementsData)) {
            return $statementsData;
        }

        return [$statementsData];
    }

    private function extractFilterParameters($paramsData): array
    {
        if (\is_array($paramsData)) {
            return $paramsData;
        }

        return [$paramsData];
    }

    private function validateFilterExpression(array $statements, array $params, Config $objConfig): void
    {
        // Check for invalid input.
        if ($this->stringHelper->testAgainstSet(strtolower(json_encode($statements)).' '.strtolower(json_encode($params)), $objConfig->getNotAllowedFilterExpr())) {
            $message = \sprintf('Illegal filter expression! Do not use "%s" in your filter expression.', implode(', ', $objConfig->getNotAllowedFilterExpr()));

            throw new \Exception($message);
        }
    }
}
