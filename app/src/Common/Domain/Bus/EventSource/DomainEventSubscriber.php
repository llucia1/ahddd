<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\Bus\EventSource;

interface DomainEventSubscriber
{
    public static function subscribedTo(): array;
}