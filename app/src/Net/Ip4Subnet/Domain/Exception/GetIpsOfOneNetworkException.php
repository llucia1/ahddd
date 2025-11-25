<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;





class GetIpsOfOneNetworkException extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('Error get all subnets of one network.'));
    }
}