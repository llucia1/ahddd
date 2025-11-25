<?php
declare(strict_types=1);
namespace GridCP\Device\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

final readonly class IpResponse implements Response
{
    public function __construct(private readonly  ?string $ip
    ){}

    public function ip(): ?string
    {
        return $this->ip;
    }
}