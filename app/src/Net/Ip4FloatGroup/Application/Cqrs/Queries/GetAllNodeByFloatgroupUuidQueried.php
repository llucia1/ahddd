<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries;

use GridCP\Common\Domain\Bus\Query\Query;

final readonly class GetAllNodeByFloatgroupUuidQueried implements Query
{
    public function __construct(private ?string $uuid){

    }

    public function uuid():?string{
        return $this->uuid;
    }

}