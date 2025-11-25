<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Cqrs\Queries;


use GridCP\Common\Domain\Bus\Query\Query;

final readonly class GetIpsByArrayNetworkIdsQueried implements Query
{
    public function __construct(private array $networks){

    }

    public function networks():?array{
        return $this->networks;
    }
}