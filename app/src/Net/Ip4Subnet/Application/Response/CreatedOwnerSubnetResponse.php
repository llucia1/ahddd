<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Response;
use Exception;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use GridCP\Common\Domain\Bus\Query\Response;

class CreatedOwnerSubnetResponse implements Response
{
    public function __construct(
                                    private null | string | Exception $owner
                                ){
                                }

    public function get(): null | string | Exception
    {
        return $this->owner;
    }
}