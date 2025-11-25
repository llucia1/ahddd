<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Tag\Application\Cqrs\Queries;

use GridCP\Common\Domain\Bus\Query\Query;

final readonly class EditTagQueried implements Query
{
    public function __construct(private string $uuid, private string $uuidIp, private ?string $tag = null, private ?bool $active = null){

    }

    public function uuid():string
    {
        return $this->uuid;
    }

    public function uuidIp():string
    {
        return $this->uuidIp;
    }
    public function tag(): ?string
    {
        return $this->tag;
    }
    public function active(): ?bool
    {
        return $this->active;
    }


}