<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Queries;

use GridCP\Common\Domain\Bus\Query\Query;

final readonly class GetIpsFreeByClientUuidQueried implements Query
{
    public function __construct(private ?string $clientUuid){

    }

    public function uuid():?string{
        return $this->clientUuid;
    }
}