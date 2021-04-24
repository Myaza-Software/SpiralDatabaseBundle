<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database;

use Spiral\Core\Container\Autowire;
use Spiral\Database\Config\DatabaseConfig;

final class DatabaseConfigBuilder
{
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
