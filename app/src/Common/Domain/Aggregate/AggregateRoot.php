<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\Aggregate;

use GridCP\Common\Domain\Bus\EventSource\DomainEvent;

abstract class AggregateRoot
{
    private array $domainEvents = [];

    final public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];
        return $domainEvents;
    }

    final protected function record(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

}