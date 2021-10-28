<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database\DependencyInjection\Compiler;

use Cycle\Bundle\Database\Logger\QueryLogger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @phpstan-type CycleConfig array{default: string, aliases: array<string,string>,databases: array<string, array<string,string>>,connections: array<string, array{driver:string, options:array<string,string>}>}
 */
final class QueryLoggerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ('dev' !== $container->getParameter('kernel.environment')) {
            return;
        }

        /** @var CycleConfig $config */
        $config         = $container->getParameter('cycle.database.vanilla_config');
        $refMonolog     = $container->hasDefinition('monolog.logger') ? new Reference('monolog.logger') : null;
        $refQueryParser = new Reference('cycle.query_analyzer');

        foreach ($config['connections'] as $name => $connection) {
            $container->register(sprintf('cycle.%s.query_logger', $name), QueryLogger::class)
                ->setArguments([
                    $name,
                    $refQueryParser,
                    $refMonolog,
                ])
                 ->addTag('cycle.query_logger')
                 ->addTag('kernel.reset', ['method' => 'reset'])
                 ->addTag('monolog.logger', ['channel' => 'cycle.dbal'])
            ;
        }
    }
}
