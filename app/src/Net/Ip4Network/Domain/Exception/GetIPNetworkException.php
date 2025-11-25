<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Exception;

class GetIPNetworkException extends \Error
{
    public function __construct(\Error $e)
    {
        parent::__construct(sprintf('Error obtain list ip_network', $e->getMessage()));
    }
}
