<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database\DependencyInjection\Compiler;

use Psr\Log\LoggerAwareInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @phpstan-type CycleConfig array{default: string, aliases: array<string,string>,databases: array<string, array<string,string>>,connections: array<string, array{driver:string, options:array<string,string>}>}
 */
final class DriverCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var CycleConfig $config */
        $config     = $container->getParameter('cycle.database.vanilla_config');
        $refMonolog = $container->hasDefinition('monolog.logger') ? new Reference('monolog.logger') : null;

        foreach ($config['connections'] as $name => ['driver' => $driver, 'options' => $options]) {
            $defDriver = $container->register(sprintf('cycle.%s.driver', $name), $driver)
                ->addArgument($options)
                ->addTag('monolog.logger', ['channel' => 'cycle.dbal'])
                ->addTag('cycle.driver')
                ->setPublic(true)
            ;

            if (!interface_exists(LoggerAwareInterface::class) || !is_subclass_of($driver, LoggerAwareInterface::class)) {
                continue;
            }

            $serviceIdQueryLogger = sprintf('cycle.%s.query_logger', $name);
            $refLogger            = null;

            if ($container->hasDefinition($serviceIdQueryLogger)) {
                $refLogger = new Reference($serviceIdQueryLogger);
            } else {
                $refLogger = $refMonolog;
            }

            if (null !== $refLogger) {
                $defDriver->addMethodCall('setLogger', [$refLogger]);
            }
        }
    }
}
