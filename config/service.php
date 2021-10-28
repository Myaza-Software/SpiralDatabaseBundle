<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

use Cycle\Bundle\Database\DatabaseConfigBuilder;
use Cycle\Bundle\Database\DataCollector\CycleDatabaseCollector;
use Cycle\Bundle\Database\QueryAnalyzer\QueryAnalyzer;
use Cycle\Bundle\Database\QueryFormatterExtension;
use Cycle\Bundle\Database\ServiceIdResolver\DriverResolver;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Spiral\Bridge\Core\ServiceFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $di): void {
    $services = $di->services();

    $services
        ->set('cycle.database.service_factory', ServiceFactory::class)
            ->args([
                service('service_container'),
                tagged_iterator('cycle.database.service_id_resolver'),
            ])

        ->set('cycle.database.driver_resolver', DriverResolver::class)
            ->tag('cycle.database.service_id_resolver')

        ->set('cycle.database.config', DatabaseConfig::class)
            ->factory([DatabaseConfigBuilder::class, 'build'])
            ->args([
                param('spiral.database.vanilla_config'),
            ])

        ->set('cycle.dbal', DatabaseManager::class)
            ->args([
                service('cycle.database.config'),
                service('cycle.database.service_factory'),
            ])
            ->public()
            ->alias(DatabaseProviderInterface::class, 'cycle.dbal')
            ->alias(DatabaseManager::class, 'cycle.dbal')

        ->set('cycle.database.collector', CycleDatabaseCollector::class)
            ->args([
                tagged_iterator('spiral.query_logger'),
                param('spiral.database.vanilla_config'),
            ])
            ->tag('data_collector', [
                'id' => 'spiral.database',
            ])

        ->set('cycle.query_analyzer', QueryAnalyzer::class)

        ->set('cycle.query_formatter.extension', QueryFormatterExtension::class)
            ->tag('twig.extension')
    ;
};
