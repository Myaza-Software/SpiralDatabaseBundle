<?php
/**
 * Cycle Database Bundle
 *
 * @author    Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database\Test\Unit;

use Cycle\Bundle\Database\DatabaseConfigBuilder;
use PHPUnit\Framework\TestCase;
use Spiral\Core\Container\Autowire;

/**
 * @phpstan-type CycleConfig array{default: string, aliases: array<string,string>,databases: array<string, array<string,string>>,connections: array<string, array{driver:string, options:array<string,string>}>}
 */
final class DatabaseConfigBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $config   = DatabaseConfigBuilder::build($this->getVanillaConfig());
        $driver   = $config->getDriver('mysql');
        $testCase = $this;

        $this->assertInstanceOf(Autowire::class, $driver);

        \Closure::bind(function (Autowire $driver) use ($testCase): void {
            $testCase->assertArrayHasKey('name', $driver->parameters);
            $testCase->assertArrayHasKey('options', $driver->parameters);
        }, null, $driver)($driver);
    }

    /**
     * @return CycleConfig
     */
    private function getVanillaConfig(): array
    {
        return [
            'default' => 'default',
            'aliases' => [
                'default'  => 'primary',
                'database' => 'primary',
                'db'       => 'primary',
            ],
            'databases' => [
                'primary' => [
                    'connection'  => 'mysql',
                    'tablePrefix' => '',
                ],
            ],
            'connections' => [
                'mysql' => [
                    'driver'  => 'mysql',
                    'options' => [
                        'connection' => 'mysql:host=127.0.0.1;dbname=test',
                        'username'   => 'test',
                        'password'   => 'test',
                    ],
                ],
            ],
        ];
    }
}
