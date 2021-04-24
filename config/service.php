<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

use Spiral\Bridge\Core\ServiceFactory;
use Spiral\Bundle\Database\DatabaseConfigBuilder;
use Spiral\Bundle\Database\DataCollector\SpiralDatabaseCollector;
use Spiral\Bundle\Database\QueryAnalyzer\QueryAnalyzer;
use Spiral\Bundle\Database\QueryFormatterExtension;
use Spiral\Bundle\Database\ServiceIdResolver\DriverResolver;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;
use Spiral\Database\DatabaseProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $di): void {
    $services = $di->services();

    $services
        ->set('spiral.database.service_factory', ServiceFactory::class)
            ->args([
                service('service_container'),
                tagged_iterator('spiral.database.service_id_resolver')
            ])

        ->set('spiral.database.driver_resolver', DriverResolver::class)
            ->tag('spiral.database.service_id_resolver')

        ->set('spiral.database.config', DatabaseConfig::class)
            ->factory([DatabaseConfigBuilder::class, 'build'])
            ->args([
                param('spiral.database.vanilla_config')
            ])

        ->set('spiral.dbal', DatabaseManager::class)
            ->args([
                service('spiral.database.config'),
                service('spiral.database.service_factory')
            ])
            ->public()
            ->alias(DatabaseProviderInterface::class,'spiral.dbal')
            ->alias(DatabaseManager::class,'spiral.dbal')

        ->set('spiral.database.collector', SpiralDatabaseCollector::class)
            ->args([
                tagged_iterator('spiral.query_logger'),
                param('spiral.database.vanilla_config')
            ])
            ->tag('data_collector',[
                'id' => 'spiral.database'
            ])

        ->set('spiral.query_analyzer', QueryAnalyzer::class)

        ->set('spiral.query_formatter.extension', QueryFormatterExtension::class)
            ->tag('twig.extension')
    ;
};
