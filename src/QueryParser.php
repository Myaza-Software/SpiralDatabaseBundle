<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database;

final class QueryParser
{
    /**
     * @param array<string,mixed> $context
     */
    public function isQuery(array $context): bool
    {
        return array_key_exists('elapsed', $context) && array_key_exists('rowCount', $context);
    }

    public function isWriteQuery(string $query): bool
    {
        if ($this->isPostgresSystemQuery($query)) {
            return false;
        }

        return 0 === strpos($query, 'insert') || 0 === strpos($query, 'update') || 0 === strpos($query, 'delete');
    }

    private function isPostgresSystemQuery(string $query): bool
    {
        $query = strtolower($query);

        return
            strpos($query, 'tc.constraint_name') ||
            strpos($query, 'pg_indexes') ||
            strpos($query, 'tc.constraint_name') ||
            strpos($query, 'pg_constraint') ||
            strpos($query, 'information_schema') ||
            strpos($query, 'pg_class');
    }
}
