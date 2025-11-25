<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Queries;

use GridCP\Common\Domain\Bus\Query\Query;

final readonly class GetAllSubnetsIgnoringTheClientQueried implements Query
{
    public function __construct(){

    }
}