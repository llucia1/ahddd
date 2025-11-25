<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Exception;

class ListIp4NetworkEmptyException extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('Not Found Ip4Networks'));
    }
}