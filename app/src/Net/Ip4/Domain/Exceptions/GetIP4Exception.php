<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Domain\Exceptions;
use Exception;

class GetIP4Exception extends Exception
{
    public function __construct(Exception $e)
    {
        parent::__construct(sprintf('Not header GridCPClient'));
    }

}