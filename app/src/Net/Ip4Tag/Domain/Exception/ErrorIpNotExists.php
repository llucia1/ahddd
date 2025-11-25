<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Domain\Exception;

use Exception;

class ErrorIpNotExists extends Exception
{
    public function __construct(string $ip)
    {
        parent::__construct("Error IP Not Exists ->".$ip);
    }

}