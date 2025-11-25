<?php
declare(strict_types=1);

namespace GridCP\Common\Infrastructure\Logger;

use GridCP\Common\Domain\Logger\Logger;


class MonoLogLogger implements Logger
{

    public function __construct(private readonly \Monolog\Logger $logger)
    {
    }


    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }
}