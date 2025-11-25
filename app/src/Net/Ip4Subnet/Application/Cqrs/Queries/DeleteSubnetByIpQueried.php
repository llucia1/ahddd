<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Queries;

use GridCP\Common\Domain\Bus\Query\Query;

final readonly class DeleteSubnetByIpQueried implements Query
{
    public function __construct( private string $ip ,  private int $mask = 32 ){
    }
    public function ip():string{
        return $this->ip;
    }
    public function mask():int{
        return $this->mask;
    }
}