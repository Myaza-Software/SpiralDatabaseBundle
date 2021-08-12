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

/**
 * @phpstan-type SpiralConfig array{default: string, aliases: array<string,string>,databases: array<string, array<string,string>>,connections: array<string, array{driver:string, options:array<string,string>}>}
 */
final class QueryLoggerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ('dev' !== $container->getParameter('kernel.environment')) {
            return;
        }

        /** @var SpiralConfig $config */
        $config         = $container->getParameter('spiral.database.vanilla_config');
        $refMonolog     = $container->hasDefinition('monolog.logger') ? new Reference('monolog.logger') : null;
        $refQueryParser = new Reference('spiral.query_analyzer');

        foreach ($config['connections'] as $name => $connection) {
            $container->register(sprintf('spiral.%s.query_logger', $name), QueryLogger::class)
                ->setArguments([
                    $name,
                    $refQueryParser,
                    $refMonolog,
                ])
                 ->addTag('spiral.query_logger')
                 ->addTag('kernel.reset', ['method' => 'reset'])
                 ->addTag('monolog.logger', ['channel' => 'spiral.dbal'])
            ;
        }
    }
}
