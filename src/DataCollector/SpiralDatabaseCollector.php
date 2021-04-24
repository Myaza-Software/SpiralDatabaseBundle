<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\DataCollector;

use Spiral\Bundle\Database\Logger\Dump;
use Spiral\Bundle\Database\Logger\QueryLogger;
use Symfony\Bundle\FrameworkBundle\DataCollector\TemplateAwareDataCollectorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SpiralDatabaseCollector implements TemplateAwareDataCollectorInterface
{
    /**
     * @var array<QueryLogger>
     */
    private $queryLoggers;

    /**
     * @var array<string,mixed>
     */
    private $config;

    /**
     * SpiralDatabaseCollector constructor.
     *
     * @param \IteratorAggregate<QueryLogger> $queryLoggers
     * @param array<string,mixed>             $config
     */
    public function __construct(\IteratorAggregate $queryLoggers, array $config)
    {
        /*
         * callback not supported serialization
         */
        $this->queryLoggers = iterator_to_array($queryLoggers);
        $this->config       = $config;
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
    }

    public static function getTemplate(): ?string
    {
        return '@SpiralDatabase/data_collector/layout.html.twig';
    }

    public function getName(): string
    {
        return 'spiral.database';
    }

    /**
     * @return \Generator<Dump>
     */
    public function dumps(): \Generator
    {
        foreach ($this->queryLoggers as $queryLogger) {
            yield $queryLogger->dump();
        }
    }

    public function hasConnections(): bool
    {
        return [] !== $this->queryLoggers;
    }

    public function isEmpty(): bool
    {
        foreach ($this->queryLoggers as $queryLogger) {
            if (!$queryLogger->dump()->isEmpty()) {
                return false;
            }
        }

        return true;
    }

    public function getTotalTimeRunQuery(): float
    {
        return $this->aggregateMetric('totalTimeRunQuery');
    }

    public function getCountReadQuery(): int
    {
        return $this->aggregateMetric('countReadQuery');
    }

    public function getCountWriteQuery(): int
    {
        return $this->aggregateMetric('countWriteQuery');
    }

    public function getTotalCountQuery(): int
    {
        return $this->aggregateMetric('totalCountQuery');
    }

    /**
     * @return \Generator<array{name:string, driver: name}>
     */
    public function getConnections(): \Generator
    {
        foreach ($this->config['connections'] as $name => $connection) {
            yield ['name' => $name, 'driver' => $connection['driver']];
        }
    }

    public function reset(): void
    {
        foreach ($this->queryLoggers as $queryLogger) {
            $queryLogger->reset();
        }
    }

    /**
     * @template T of int|float
     *
     * @return T
     */
    private function aggregateMetric(string $property)
    {
        $total = 0;

        foreach ($this->queryLoggers  as $queryLogger) {
            $total += call_user_func([$queryLogger->dump(), 'get' . ucfirst($property)]);
        }

        return $total;
    }
}
