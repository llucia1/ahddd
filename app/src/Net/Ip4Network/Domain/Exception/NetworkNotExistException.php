<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Exception;



final class NetworkNotExistException extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf("Network Not Exist "));
    }
}