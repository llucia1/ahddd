<?php

namespace GridCP\Net\Ip4\Domain\Service;

use GridCP\Net\Ip4\Application\Response\Ip4sResponse;

interface IListIp4Service
{
    function getAll():Ip4sResponse;

    function toResponse():callable;

}