<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Cqrs\Queries;


use GridCP\Common\Domain\Bus\Query\Query;

final readonly class GetAllIp4sOfOneNetworkByUuidQueried implements Query
{
    public function __construct(private string $networkUuid){

    }

    public function uuid():?string{
        return $this->networkUuid;
    }
}