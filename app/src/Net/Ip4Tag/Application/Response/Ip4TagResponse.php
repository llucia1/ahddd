<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Application\Response;
use GridCP\Common\Domain\Bus\Query\Response;

class Ip4TagResponse implements Response
{
    public function __construct(
        private string $uuid,
        private ?string  $tag = null,
        private $ip4 = null,
        ) {}

    public function uuid():string
    {
        return $this->uuid;
    }

    public function ip4():mixed
    {
        return $this->ip4;
    }

    public function tag():?string
    {
        return $this->tag;
    }
}