<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;


class SubnetNoFound extends \Exception
{
    public function __construct( string $subnetUuid )
    {
        parent::__construct('Not Found Subnet with uuid = ' . $subnetUuid);
    }
}