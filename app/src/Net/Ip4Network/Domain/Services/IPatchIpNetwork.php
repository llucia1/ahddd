<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Services;


use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkPatch;

interface IPatchIpNetwork
{
    function patchIPNetwork(Ip4NetworkPatch $ip4Network, string $uuidIPNetwork):void;
}