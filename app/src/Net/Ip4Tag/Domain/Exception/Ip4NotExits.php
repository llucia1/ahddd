<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Domain\Exception;

use Exception;

class Ip4NotExits extends Exception
{
    public function __construct()
    {
        parent::__construct('Ip4 Not Exits');
    }

}