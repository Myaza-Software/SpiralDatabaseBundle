<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\Logger;

final class Query
{
    /**
     * @var string
     */
    private $sql;

    /**
     * @var float
     */
    private $elapsed;

    /**
     * @var int
     */
    private $rowCount;

    /**
     * Query constructor.
     */
    public function __construct(string $sql, float $elapsed, int $rowCount)
    {
        $this->sql      = $sql;
        $this->elapsed  = $elapsed;
        $this->rowCount = $rowCount;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getElapsed(): float
    {
        return $this->elapsed;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
