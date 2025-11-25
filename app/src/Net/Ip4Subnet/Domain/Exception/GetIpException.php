<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;





class GetIpException extends \Exception
{
    public function __construct(?string $msn = null)
    {
        parent::__construct(sprintf('Not Found Ip %s', ($msn ? '-> '.$msn : '')), 404);
    }
}