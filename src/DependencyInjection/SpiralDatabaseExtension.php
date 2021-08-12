<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\DependencyInjection;

use Spiral\Database\Driver\DriverInterface;
use Spiral\Database\Driver\MySQL\MySQLDriver;
use Spiral\Database\Driver\Postgres\PostgresDriver;
use Spiral\Database\Driver\SQLite\SQLiteDriver;
use Spiral\Database\Driver\SQLServer\SQLServerDriver;
use Spiral\Database\Exception\DriverException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @phpstan-type SpiralConfig array{default: string, aliases: array<string,string>,databases: array<string, array<string,string>>,connections: array<string, array{driver:string, options:array<string,string>}>}
 */
final class SpiralDatabaseExtension extends Extension
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

        $container->setParameter('spiral.database.vanilla_config', $config);
    }

    /**
     * @param array<string,string>   $configs
     *
     * @return SpiralConfig
     */
    private function getVanillaConfiguration(ConfigurationInterface $configuration, array $configs): array
    {
        /** @var SpiralConfig $config */
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
