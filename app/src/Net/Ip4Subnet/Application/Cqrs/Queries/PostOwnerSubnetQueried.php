<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Queries;

use GridCP\Common\Domain\Bus\Query\Query;

final readonly class PostOwnerSubnetQueried implements Query
{
    public function __construct(private ?string $clientUuid, private string $subnetUuid){
    }

    public function clientUuid():?string{
        return $this->clientUuid;
    }
    public function subnetuuid():string{
        return $this->subnetUuid;
    }
}