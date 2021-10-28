<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database\Test\Unit\ServiceIdResolver;

use Cycle\Bundle\Database\ServiceIdResolver\DriverResolver;
use Cycle\Database\Driver\MySQL\MySQLDriver;
use PHPUnit\Framework\TestCase;

final class DriverResolverTest extends TestCase
{
    /**
     * @dataProvider supportDataProvider
     *
     * @param array<string,string> $parameters
     */
    public function testSupport(string $driver, array $parameters, bool $status): void
    {
        $resolver = new DriverResolver();

        $this->assertEquals($status, $resolver->support($driver, $parameters));
    }

    /**
     * @return array<int, array{0: class-string, 1: array<string,string>, 2: bool}>
     */
    public function supportDataProvider(): array
    {
        return [
            [MySQLDriver::class, ['name' => 'null'], true],
            [\stdClass::class, ['name' => 'null'], false],
            [MySQLDriver::class, ['test' => 'null'], false],
        ];
    }

    public function testResolve(): void
    {
        $parameters = ['name' => 'null'];
        $resolver   = new DriverResolver();
        $expected   = sprintf('Cycle.%s.driver', $parameters['name']);

        $this->assertEquals($expected, $resolver->resolve(MySQLDriver::class, $parameters));
    }
}
