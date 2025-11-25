<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;





class GetVmException extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('Not Found Vm. Error.'), 404);
    }
}