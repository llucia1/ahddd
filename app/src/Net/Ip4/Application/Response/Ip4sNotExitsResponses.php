<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

final readonly class Ip4sNotExitsResponses implements Response
{
    private ?array $ip4s;

    public function __construct(?array $ip4s)
    {
        $this->ip4s = $ip4s;
    }

    public function gets(): ?array
    {
        return $this->ip4s;
    }
}