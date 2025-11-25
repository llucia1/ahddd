<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\Logger;

interface Logger
{
    public function info(string $message, array $context = []): void;

    public function warning(string $message, array $context = []): void;

    public function error(string $message, array $context = []): void;

}