<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Queries;

use GridCP\Common\Domain\Bus\Query\Query;

final readonly class PostSubnetQueried implements Query
{
    public function __construct(private string $floatgroupUuid, private string $ip , private int $mask = 32, private ?string $uuid = null){
    }

    public function floatgroupUuid():string{
        return $this->floatgroupUuid;
    }
    public function mask():int{
        return $this->mask;
    }
    public function ip():string{
        return $this->ip;
    }
    public function uuid():?string{
        return $this->uuid;
    }
}