<?php

namespace GridCP\Net\Ip4\Domain\Service;


use GridCP\Net\Ip4\Domain\VO\PatchIp4Vo;

interface IEditIp4Service
{
    public function editIP4(PatchIp4Vo $ip4):array;

}