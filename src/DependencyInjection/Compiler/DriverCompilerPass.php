<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\DependencyInjection\Compiler;

use Psr\Log\LoggerAwareInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class DriverCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $config    = $container->getParameter('spiral.database.vanilla_config');
        $refLogger = null;

        if ($container->hasDefinition('spiral.query_logger')) {
            $refLogger = new Reference('spiral.query_logger');
        } elseif ($container->hasDefinition('monolog.logger')) {
            $refLogger = new Reference('monolog.logger');
        }

        foreach ($config['connections'] as $name => ['driver' => $driver, 'options' => $options]) {
            $defDriver = $container->register(sprintf('spiral.%s.driver', $name), $driver)
                ->addArgument($options)
                ->addTag('monolog.logger', ['channel' => 'spiral.dbal'])
                ->setPublic(true)
            ;

            if (!interface_exists(LoggerAwareInterface::class) || !is_subclass_of($driver, LoggerAwareInterface::class)) {
                continue;
            }

            if (null !== $refLogger) {
                $defDriver->addMethodCall('setLogger', [$refLogger]);
            }
        }
    }
}
