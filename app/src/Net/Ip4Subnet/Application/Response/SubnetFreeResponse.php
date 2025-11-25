<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

final readonly class SubnetFreeResponse implements Response
{
    public function __construct(
        private ?array $ips,
        private ?int $mask
    ) { }

    public function ips(): ?array
    {
        return $this->ips;
    }

    public function mask(): ?int
    {
        return $this->mask;
    }
}
