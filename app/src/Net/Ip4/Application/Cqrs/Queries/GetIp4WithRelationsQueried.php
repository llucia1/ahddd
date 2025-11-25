<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Cqrs\Queries;


use GridCP\Common\Domain\Bus\Query\Query;

final readonly class GetIp4WithRelationsQueried implements Query
{
    public function __construct(private string $ip){

    }

    public function ip():?string{
        return $this->ip;
    }
}