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
     * @var QueryLogger
     */
    private $queryLogger;

    /**
     * SpiralDatabaseCollector constructor.
     */
    public function __construct(QueryLogger $queryLogger)
    {
        $this->queryLogger = $queryLogger;
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
    }

    public static function getTemplate(): ?string
    {
        return '@SpiralDatabase/data_collector/template.html.twig';
    }

    public function getName(): string
    {
        return 'spiral.database';
    }

    public function dump(): Dump
    {
        return $this->queryLogger->dump();
    }

    public function reset(): void
    {
        $this->queryLogger->reset();
    }
}
