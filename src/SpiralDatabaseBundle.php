<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database;

use Spiral\Bundle\Database\DependencyInjection\Compiler\DatabaseCompiler;
use Spiral\Bundle\Database\DependencyInjection\Compiler\DriverCompilerPass;
use Spiral\Bundle\Database\DependencyInjection\Compiler\QueryLoggerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SpiralDatabaseBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new QueryLoggerCompilerPass());
        $container->addCompilerPass(new DriverCompilerPass());
        $container->addCompilerPass(new DatabaseCompiler());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
