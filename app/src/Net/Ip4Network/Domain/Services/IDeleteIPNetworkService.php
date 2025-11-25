<?php
namespace GridCP\Net\Ip4Network\Domain\Services;

use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;


interface IDeleteIPNetworkService
{
    public function delete(Ip4NetworkUUID $uuid): ?string;
}