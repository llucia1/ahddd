<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Exception;



final class ErrorFloatGroupOrNetworkNotExist extends \Error
{
    public function __construct(string $msnError)
    {
        parent::__construct(sprintf("Error float Group or Network Not Exist ->".$msnError));
    }
}