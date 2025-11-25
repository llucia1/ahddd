<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;


class SubnetDuplicated extends \Exception
{
    public function __construct( string $subnetUuid )
    {
        parent::__construct('Duplicated Subnet with uuid = ' . $subnetUuid);
    }
}