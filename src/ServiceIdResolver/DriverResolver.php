<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database\ServiceIdResolver;

use Spiral\Bridge\Core\ServiceIdResolverInterface;
use Cycle\Database\Driver\DriverInterface;

final class DriverResolver implements ServiceIdResolverInterface
{
    public function support(string $class, array $parameters): bool
    {
        return is_subclass_of($class, DriverInterface::class) && array_key_exists('name', $parameters);
    }

    public function resolve(string $class, array $parameters): string
    {
        return sprintf('Cycle.%s.driver', $parameters['name']);
    }
}
