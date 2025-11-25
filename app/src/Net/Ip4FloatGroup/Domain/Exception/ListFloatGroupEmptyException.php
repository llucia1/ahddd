<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Domain\Exception;

class ListFloatGroupEmptyException extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('Not Found Float Groups'));
    }
}