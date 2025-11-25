<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

final class Ip4sResponse implements Response
{
    private readonly array $ip4s;

    public function __construct(Ip4Response ...$ip4s)
    {
        $this->ip4s = $ip4s;
    }

    public function ip4s(): array
    {
        return $this->ip4s;
    }

}