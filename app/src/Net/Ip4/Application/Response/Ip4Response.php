<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Response;

use GridCP\Net\Ip4Tag\Application\Response\Ip4TagResponse;

final readonly class Ip4Response
{
    public function __construct(
        private string $uuid,
        private string $ip,
        private ?Ip4WithNetworkResponse  $network = null,
        private ?bool   $active = null,
        private ?int $priority = 1,
        private ?Ip4TagResponse $tag = null
    ) {}
    public function uuid():string
    {
        return $this->uuid;
    }

    public function ip():string
    {
        return $this->ip;
    }

    public function network():?Ip4WithNetworkResponse
    {
        return $this->network;
    }

    public function active():?bool
    {
        return $this->active;
    }

    public function priority():?int
    {
        return $this->priority;
    }

    public function tag():?Ip4TagResponse
    {
        return $this->tag;
    }
}