<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Domain\Exception;



final class ErrorFloatGroupNotExist extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf("Error float Group Not Exist."), 404);
    }
}