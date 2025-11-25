<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Application\Responses;
use GridCP\Common\Domain\Bus\Query\Response;

final readonly class FloatGroupResponse  implements Response
{

 public function __construct(private ?string $uuid, private ?string $name, private ?bool $active, private ?int $id = null , private ?array $netwoks=[], private ?array $floatgroup=[]){}

    public function uuid():?string
    {
        return $this->uuid;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function active():?bool
    {
        return $this->active;
    }

    public function networks():?array
    {
        return $this->netwoks;
    }
    public function floatgroup():?array
    {
        return $this->floatgroup;
    }
    public function id():?int
    {
        return $this->id;
    }
}