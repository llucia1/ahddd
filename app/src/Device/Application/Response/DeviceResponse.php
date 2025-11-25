<?php
declare(strict_types=1);

namespace GridCP\Device\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

final readonly class DeviceResponse implements Response
{
    public function __construct(
        private string $uuid,
        private string $ip,
        private string $device,
        private string $country,
        private string $location   
    ) {
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