<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;


class PropertySubnetNotFound extends \Exception
{
    public function __construct( )
    {
        parent::__construct('Onwer Subnet not found.');
    }
}