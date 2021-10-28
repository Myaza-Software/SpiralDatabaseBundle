<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database\DependencyInjection;

use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\Driver\MySQL\MySQLDriver;
use Cycle\Database\Driver\Postgres\PostgresDriver;
use Cycle\Database\Driver\SQLite\SQLiteDriver;
use Cycle\Database\Driver\SQLServer\SQLServerDriver;
use Cycle\Database\Exception\DriverException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @phpstan-type CycleConfig array{default: string, aliases: array<string,string>,databases: array<string, array<string,string>>,connections: array<string, array{driver:string, options:array<string,string>}>}
 */
final class CycleDatabaseExtension extends Extension
{
    private const DRIVERS = [
        'mysql'  => MySQLDriver::class,
        'sqlite' => SQLiteDriver::class,
        'sqlsrv' => SQLServerDriver::class,
        'pgsql'  => PostgresDriver::class,
    ];

    /**
     * @param array<string,string> $configs
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        $loader->load('service.php');

        $configuration = new Configuration();
        $config        = $this->getVanillaConfiguration($configuration, $configs);

        $container->setParameter('cycle.database.vanilla_config', $config);
    }

    /**
     * @param array<string,string> $configs
     *
     * @return CycleConfig
     */
    private function getVanillaConfiguration(ConfigurationInterface $configuration, array $configs): array
    {
        /** @var CycleConfig $config */
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['connections'] as &$connection) {
            $driver = self::DRIVERS[$connection['driver']] ?? $connection['driver'];

            if (!$this->isSupportDriver($driver)) {
                throw new DriverException(sprintf('This dbal driver %s does not support', $driver));
            }

            $connection['driver'] = $driver;
        }

        return $config;
    }

    private function isSupportDriver(string $driver): bool
    {
        if (!class_exists($driver) && !is_subclass_of($driver, DriverInterface::class)) {
            return false;
        }

        return true;
    }
}
