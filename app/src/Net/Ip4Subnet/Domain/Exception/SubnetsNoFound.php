<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;


class SubnetsNoFound extends \Exception
{
    public function __construct( )
    {
        parent::__construct('Not Found Subnets. ');
    }
}