<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\DependencyInjection\Compiler;

use Spiral\Bundle\Database\Logger\QueryLogger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class QueryLoggerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ('dev' !== $container->getParameter('kernel.environment')) {
            return;
        }

        $container->register('spiral.query_logger', QueryLogger::class)
            ->setArguments([
                new Reference('spiral.query_parser'),
                $container->hasDefinition('monolog.logger') ? new Reference('monolog.logger') : null,
            ])
            ->addTag('monolog.logger', ['channel' => 'spiral.dbal'])
            ->addTag('kernel.reset', ['method' => 'reset'])
        ;
    }
}
