<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\Test\Unit\QueryAnalyzer;

use PHPUnit\Framework\TestCase;
use Spiral\Bundle\Database\QueryAnalyzer\QueryAnalyzer;

final class QueryAnalyzerTest extends TestCase
{
    /**
     * @dataProvider analyzeHasQueryDataProvider
     *
     * @param array<string,mixed> $context
     */
    public function testAnalyzeHasQuery(string $sql, array $context, bool $hasQuery): void
    {
        $queryAnalyzer = new QueryAnalyzer();
        $analysis      = $queryAnalyzer->analyze($sql, $context);

        $this->assertEquals($hasQuery, $analysis->hasQuery());
    }

    /**
     * @return array<int,array{0: string, 1: array<string,float|int>, 2: bool}>
     */
    public function analyzeHasQueryDataProvider(): array
    {
        return [
            ['SELECT * FROM users', ['elapsed' => 0.3, 'rowCount' => 2], true],
            ['SELECT * FROM users', ['rowCount' => 2], false],
        ];
    }

    /**
     * @dataProvider analyzeIsWriteQueryDataProvider
     *
     * @param array<string,mixed> $context
     */
    public function testAnalyzeIsWriteQuery(string $sql, array $context, bool $isWrite): void
    {
        $queryAnalyzer = new QueryAnalyzer();
        $query         = $queryAnalyzer->analyze($sql, $context)->getQuery();

        $this->assertEquals($isWrite, $query->isWrite());
    }

    /**
     * @return array<int,array{0: string, 1: array<string,float|int>, 2: bool}>
     */
    public function analyzeIsWriteQueryDataProvider(): array
    {
        return [
            ['SELECT * FROM users', ['elapsed' => 0.3, 'rowCount' => 2], false],
            ['INSERT INTO users VALUES ("Vlad Shashkov", 23)', ['elapsed' => 0.3, 'rowCount' => 2], true],
            ['UPDATE users SET name = "Vlad Shashkov"', ['elapsed' => 0.3, 'rowCount' => 2], true],
            ['DELETE FROM users where name = "Vlad Shashkov"', ['elapsed' => 0.3, 'rowCount' => 2], true],
            ['ALTER TABLE users ADD age integer', ['elapsed' => 0.3, 'rowCount' => 2], true],
            ['DROP TABLE users', ['elapsed' => 0.3, 'rowCount' => 2], true],
        ];
    }

    public function testAnalyzeNotFoundQuery(): void
    {
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Not found query');
        $this->expectException(\LogicException::class);

        $queryAnalyzer = new QueryAnalyzer();

        $queryAnalyzer->analyze('Go go', [])->getQuery();
    }
}
