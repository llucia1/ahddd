<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Queries;

use GridCP\Common\Domain\Bus\Query\Query;

final readonly class GetAllSubnetsQueried implements Query
{
    public function __construct(private ?string $clientUuid = null){

    }

    public function clientUuid():?string{
        return $this->clientUuid;
    }
}