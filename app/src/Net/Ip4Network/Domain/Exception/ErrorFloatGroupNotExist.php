<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Exception;



final class ErrorFloatGroupNotExist extends \Exception
{
    public function __construct(string $floatGroup)
    {
        parent::__construct(sprintf("Error float Group Not Exits ->".$floatGroup));
    }
}