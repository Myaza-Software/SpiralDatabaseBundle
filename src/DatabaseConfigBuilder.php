<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database;

use Cycle\Database\Config\DatabaseConfig;
use Spiral\Core\Container\Autowire;

/**
 * @internal
 * @phpstan-type CycleConfig array{default: string, aliases: array<string,string>,databases: array<string, array<string,string>>,connections: array<string, array{driver:string, options:array<string,string>}>}
 */
final class DatabaseConfigBuilder
{
    /**
     * @param CycleConfig $config
     *
     * @return DatabaseConfig<CycleConfig>
     */
    public static function build(array $config): DatabaseConfig
    {
        foreach ($config['connections'] as $name => &$connection) {
            $driver = $connection['driver'];

            $connection = new Autowire(
                $driver,
                ['options' => $connection['options'], 'name' => $name]
            );
        }

        return new DatabaseConfig($config);
    }
}
