<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Domain\VO;

use GridCP\Common\Domain\Aggregate\AggregateRoot;

class Ip4FloatGroupsPacth extends AggregateRoot
{
    public function __construct(
        private readonly ?Ip4FloatGroupsUuid   $uuid,
        private readonly ?Ip4FloatGroupsName   $name,
        private readonly ?Ip4FloatGroupsActive $active=null)
    {
    }

    public static function create(?Ip4FloatGroupsUuid $uuid=null, ?Ip4FloatGroupsName $name=null, ?Ip4FloatGroupsActive $active=null): self
    {
        return new self($uuid, $name, $active);
    }

    public function uuid(): ?Ip4FloatGroupsUuid
    {
        return $this->uuid;
    }

    public function name(): ?Ip4FloatGroupsName
    {
        return $this->name;
    }

    public function active():?Ip4FloatGroupsActive
    {
        return $this->active;
    }
}