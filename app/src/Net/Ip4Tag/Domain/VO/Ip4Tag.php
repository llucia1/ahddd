<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Domain\VO;

use GridCP\Common\Domain\Aggregate\AggregateRoot;
use GridCP\Common\Domain\ValueObjects\Ip4TagActive;
use GridCP\Common\Domain\ValueObjects\Ip4TagTag;
class Ip4Tag extends AggregateRoot
{
    public function __construct(
        private readonly Ip4TagUuid $uuid,
        private readonly ?Ip4TagUuidIp $uuidIp = null,
        private readonly ?Ip4TagTag $tag = null,
        private readonly ?Ip4TagActive $active = null
    )
    {
    }

    public function create(Ip4TagUuid $uuid, ?Ip4TagUuidIp $uuidIp, ?Ip4TagTag $tag, ?Ip4TagActive $active = null): self
    {
        return new self($uuid, $uuidIp, $tag, $active );
    }

    public function Uuid(): Ip4TagUuid
    {
        return $this->uuid;
    }

    public function uuidIp(): ?Ip4TagUuidIp
    {
        return $this->uuidIp;
    }

    public function Tag(): ?Ip4TagTag
    {
        return $this->tag;
    }

    public function active(): ?Ip4TagActive
    {
        return $this->active;
    }

}