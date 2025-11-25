<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Services;

use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworksResponse;

interface IListIp4NetworkService
{
    function getAll():Ip4NetworksResponse;
    function toResponse():callable;
}