<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Domain\VO;

use GridCP\Common\Domain\Aggregate\AggregateRoot;
use GridCP\Common\Domain\ValueObjects\Ip4TagTag;

class PatchIp4Vo extends AggregateRoot
{

    public function __construct(
        private readonly ?Ip4Ip        $ip4,
        private readonly ?Ip4UuidNetwork $idNetwork,
        private readonly ?Ip4Priority $priority,
        private readonly ?Ip4TagTag $tag = null
    ){}

    public static function create(?Ip4Ip $ip4, ?Ip4UuidNetwork $network, ?Ip4Priority $priority, ?Ip4TagTag $tag = null ): self
    {
        return new self($ip4, $network, $priority, $tag  );
    }

    public function Ip4(): ?Ip4Ip
    {
        return $this->ip4;
    }

    public function UuidNetwork(): ?Ip4UuidNetwork
    {
        return $this->idNetwork;
    }

    public function Priority(): ?Ip4Priority
    {
        return $this->priority;
    }

    public function Tag(): ?Ip4TagTag
    {
        return $this->tag;
    }
}