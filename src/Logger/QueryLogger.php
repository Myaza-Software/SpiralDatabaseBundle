<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\NullLogger;
use Spiral\Bundle\Database\QueryParser;
use Symfony\Contracts\Service\ResetInterface;

final class QueryLogger implements LoggerInterface, ResetInterface
{
    use LoggerTrait;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Dump
     */
    private $dump;

    /**
     * @var QueryParser
     */
    private $queryParser;

    /**
     * QueryLogger constructor.
     */
    public function __construct(QueryParser $queryParser, ?LoggerInterface $logger = null)
    {
        $this->dump        = new Dump();
        $this->logger      = $logger ?? new NullLogger();
        $this->queryParser = $queryParser;
    }

    /**
     * @param array<string,mixed> $context
     */
    public function log($level, $message, array $context = []): void
    {
        if ($this->queryParser->isQuery($context)) {
            if ($this->queryParser->isWriteQuery($message)) {
                $this->dump->incrementWriteQuery();
            } else {
                $this->dump->incrementReadQuery();
            }

            $this->dump->addQuery(new Query($message, $context['elapsed'], $context['rowCount']));
        }

        $this->logger->log($level, $message, $context);
    }

    public function dump(): Dump
    {
        return $this->dump;
    }

    public function reset(): void
    {
        $this->dump = new Dump();
    }
}
