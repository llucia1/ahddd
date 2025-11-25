<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Exception;

class ListIp4EmptyException extends Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('Not Found Ip4s'));
    }

}