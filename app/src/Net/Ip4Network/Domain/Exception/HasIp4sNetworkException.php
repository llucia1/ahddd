<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Exception;

class HasIp4sNetworkException extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('This Network has associated Ip4s'));
    }
}