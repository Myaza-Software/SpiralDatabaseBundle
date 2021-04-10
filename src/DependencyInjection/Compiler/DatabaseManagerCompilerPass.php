<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\DependencyInjection\Compiler;

use Spiral\Database\DatabaseManager;
use Spiral\Database\DatabaseProviderInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class DatabaseManagerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $dbal = $container
            ->register('spiral.dbal', DatabaseManager::class)
            ->setArguments([
                new Reference('spiral.database.config'),
                new Reference('spiral.database.service_factory'),
            ])
            ->setPublic(true)
        ;

        $refLogger = null;

        if ($container->hasDefinition('spiral.query_logger')) {
            $refLogger = new Reference('spiral.query_logger');
        } elseif ($container->hasDefinition('monolog.logger')) {
            $refLogger = new Reference('monolog.logger');
        }

        if (null !== $refLogger) {
            $dbal
                ->addTag('monolog.logger', ['channel' => 'spiral.dbal'])
                ->addMethodCall('setLogger', [$refLogger])
            ;
        }

        $container->setAlias(DatabaseProviderInterface::class, new Alias('spiral.dbal', true));
        $container->setAlias(DatabaseManager::class, new Alias('spiral.dbal', true));
    }
}
