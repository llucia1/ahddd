<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

final readonly class Ip4SubnetAvaibleResponse implements Response
{
    public function __construct(
        private ?string $ip = null,
        private ?int $mask = null,
        private ?string $uuid = null
    ) {
    }
    
    public function ip(): ?string
    {
        return $this->ip;
    }

    public function mask(): ?int
    {
        return $this->mask;
    }
    public function uuid(): ?string
    {
        return $this->uuid;
    }
}
