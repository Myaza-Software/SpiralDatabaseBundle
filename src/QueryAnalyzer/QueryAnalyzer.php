<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database\QueryAnalyzer;

use Cycle\Bundle\Database\Logger\Query;

final class QueryAnalyzer
{
    private const QUERY_WRITE_PATTERN = [
        'insert',
        'update',
        'delete',
        'create',
        'alter',
        'drop',
    ];

    /**
     * @param array<string,mixed> $context
     */
    public function analyze(string $text, array $context): Analysis
    {
        if (!$this->isQuery($context)) {
            return new Analysis();
        }

        $query = new Query(
            $text,
            $context['elapsed'],
            $context['rowCount'],
            $this->isWriteQuery($text)
        );

        return new Analysis($query);
    }

    /**
     * @param array<string,mixed> $context
     */
    private function isQuery(array $context): bool
    {
        return array_key_exists('elapsed', $context) && array_key_exists('rowCount', $context);
    }

    private function isWriteQuery(string $query): bool
    {
        $query = strtolower($query);

        foreach (self::QUERY_WRITE_PATTERN as $pattern) {
            if (0 === strpos($query, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
