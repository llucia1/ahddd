<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Response;

use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use GridCP\Common\Domain\Bus\Query\Response;

class SubnetEntityResponse implements Response
{
    public function __construct(
                                    private ?Ip4SubnetEntity $subnet
                                ){
                                }

    public function get(): ?Ip4SubnetEntity
    {
        return $this->subnet;
    }
}