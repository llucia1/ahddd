<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Services;

use GridCP\Net\Ip4Network\Domain\VO\Ip4Network;

interface ICreateIpNetwork
{
    function createIPNetwork(Ip4Network $ip4Network):string;
}