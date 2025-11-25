<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\Bus\EventSource;

use DateTimeImmutable;
use GridCP\Common\Domain\Utils;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;

abstract class DomainEvent
{
    private readonly string $eventId;
    private readonly string $eventTime;

    public function __construct(private readonly string $aggregateId, string $eventId = null, string $eventTime = null)
    {
        $this->eventId = $eventId ?: UuidValueObject::random()->value();
        $this->eventTime = $eventTime ?: Utils::dateToString(new DateTimeImmutable());
    }

    abstract public static function fromPrimitives(
        string $aggregateId,
        array  $body,
        string $eventId,
        string $eventTime
    ): self;

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function eventTime(): string
    {
        return $this->eventTime;
    }
}