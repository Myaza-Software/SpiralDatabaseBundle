<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\Logger;

final class Dump
{
    /**
     * @var int
     */
    private $countReadQuery = 0;

    /**
     * @var int
     */
    private $countWriteQuery = 0;

    /**
     * @var array<Query>
     */
    private $queries = [];

    public function addQuery(Query $query): void
    {
        $this->queries[] = $query;
    }

    public function incrementReadQuery(): void
    {
        ++$this->countReadQuery;
    }

    public function incrementWriteQuery(): void
    {
        ++$this->countWriteQuery;
    }

    public function getCountReadQuery(): int
    {
        return $this->countReadQuery;
    }

    public function getCountWriteQuery(): int
    {
        return $this->countWriteQuery;
    }

    /**
     * @return Query[]
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    public function isEmpty(): bool
    {
        return [] === $this->queries;
    }

    public function getTotalCountQuery(): int
    {
        return $this->countReadQuery + $this->countWriteQuery;
    }

    public function getTotalTimeRunQuery(): float
    {
        $total = 0;

        foreach ($this->queries as $query) {
            $total += $query->getElapsed();
        }

        return $total;
    }
}
