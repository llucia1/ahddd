<?php

namespace GridCP\Net\Ip4FloatGroup\Domain\Exception;

class FloatGroupHasAssociatedNodes extends \Exception
{
public function __construct()
    {
        parent::__construct('FloatGroup has Associated Nodes');
    }

}