<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database\DependencyInjection\Compiler;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @phpstan-type CycleConfig array{default: string, aliases: array<string,string>,databases: array<string, array<string,string>>,connections: array<string, array{driver:string, options:array<string,string>}>}
 */
final class DatabaseCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var CycleConfig $config */
        $config = $container->getParameter('cycle.database.vanilla_config');

        foreach ($config['databases'] as $name => $database) {
            $id           = sprintf('cycle.%s.database', $name);
            $argumentName = sprintf('%sDatabase', ucfirst($name));

            $container->register($id, Database::class)
                ->addArgument($name)
                ->setFactory([new Reference('cycle.dbal'), 'database'])
                ->addTag('cycle.dbal.db');

            $container
                ->registerAliasForArgument($id, Database::class, $argumentName)
                ->setPublic(true)
            ;
            $container
                ->registerAliasForArgument($id, DatabaseInterface::class, $argumentName)
                ->setPublic(true)
            ;
        }
    }
}
