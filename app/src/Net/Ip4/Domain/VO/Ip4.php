<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Domain\VO;

use GridCP\Common\Domain\Aggregate\AggregateRoot;
use GridCP\Common\Domain\ValueObjects\Ip4TagTag;

class Ip4 extends AggregateRoot
{

    public function __construct(
        private readonly Ip4Uuid      $uuid,
        private readonly Ip4Ip        $ip4,
        private readonly Ip4UuidNetwork $idNetwork,
        private readonly Ip4Active    $ip4Active,
        private readonly Ip4Priority $priority,
        private readonly ?Ip4TagTag $tag = null
    ){}

    public static function create(Ip4Uuid $uuid, Ip4Ip $ip4, Ip4UuidNetwork $network, Ip4Active $active, Ip4Priority $priority, ?Ip4TagTag $tag = null): self
    {
        return new self($uuid, $ip4, $network, $active, $priority, $tag );
    }
    public function Uuid(): Ip4Uuid
    {
        return $this->uuid;
    }

    public function Ip4(): Ip4Ip
    {
        return $this->ip4;
    }

    public function UuidNetwork(): Ip4UuidNetwork
    {
        return $this->idNetwork;
    }

    public function Active(): Ip4Active
    {
        return $this->ip4Active;
    }

    public function Priority(): Ip4Priority
    {
        return $this->priority;
    }

    public function Tag(): ?Ip4TagTag
    {
        return $this->tag;
    }
}