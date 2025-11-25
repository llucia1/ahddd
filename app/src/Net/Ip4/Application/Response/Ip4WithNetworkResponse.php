<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Response;

final readonly class Ip4WithNetworkResponse
{
    public function __construct(
        private string $uuid,
        private string $name) {}

    public function uuid():string
    {
        return $this->uuid;
    }
    public function name():string
    {
        return $this->name;
    }
}