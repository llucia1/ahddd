<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Domain\Exception;

use Exception;

class Ip4TagNotExits extends Exception
{
    public function __construct()
    {
        parent::__construct('Tag Ip4 Not Exits');
    }

}