<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

final readonly class OwnerResponse implements Response
{
    
    public function __construct(
        private ?string $uuid,
        private ?string $name,
    ) { }

    public function uuid(): ?string
    {
        return $this->uuid;
    }

    public function name(): ?string
    {
        return $this->name;
    }
}
