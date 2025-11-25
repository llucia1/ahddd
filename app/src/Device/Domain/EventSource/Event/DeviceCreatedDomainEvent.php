<?php

namespace GridCP\Device\Domain\EventSource\Event;

use GridCP\Common\Domain\Bus\EventSource\DomainEvent;

class DeviceCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        string                  $id,
        private readonly ?string $uuid = null,
        private readonly ?string $ip = null,
        private readonly ?string $device = null,
        private readonly ?string $country = null,
        private readonly ?string $location = null,
        string                  $eventId = null,
        string                  $eventTime = null
    )
    {
        parent::__construct($id, $eventId, $eventTime);
    }

    public static function fromPrimitives(
        string $aggregateId,
        array  $body,
        string $eventId,
        string $eventTime
    ): DomainEvent
    {
        return new self($aggregateId, $body['uuid'], $body['ip'], $body['device'], $body['country'], $body['location'],  $eventId, $eventTime);
    }

    public static function eventName(): string
    {
        return 'device.created';
    }

    public function toPrimitives(): array
    {
        return [
            'uuid' => $this->uuid,
            'ip' => $this->ip,
            'device' => $this->device,
            'country' => $this->country,
            'location' => $this->location,
        ];
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function ip(): string
    {
        return $this->ip;
    }

    public function device(): string
    {
        return $this->device;
    }

    public function country(): string
    {
        return $this->country;
    }

    public function location(): string
    {
        return $this->location;
    }
}