<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;




class MaskNotValid extends \Exception
{
    public function __construct(String $mask)
    {
        parent::__construct('Mask not valid -> ' . $mask . ' . Mask must be between 20 and 32');
    }

}