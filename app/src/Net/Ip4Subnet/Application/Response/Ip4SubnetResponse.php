<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

final readonly class Ip4SubnetResponse implements Response
{
    public function __construct(
        private ?string $uuid,
        private ?string $ip,
        private ?int $mask,
        private ?FloatgroupResponse $floatgroupUuid,
        private ?OwnerResponse $owner

    ) { }
    public function uuid(): ?string
    {
        return $this->uuid;
    }

    public function ip(): ?string
    {
        return $this->ip;
    }

    public function mask(): ?int
    {
        return $this->mask;
    }

    public function floatgroup(): ?FloatgroupResponse
    {
        return $this->floatgroupUuid;
    }

    public function owner(): ?OwnerResponse
    {
        return $this->owner;
    }
}
