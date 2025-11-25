<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Domain\Exception;

use Exception;

class Ip4TagDuplicated extends Exception
{
    public function __construct()
    {
        parent::__construct('Ip4 Tag Duplicated.');
    }

}