<?php
declare(strict_types=1);
namespace Tests\Proxmox\Vm\Application\Service\So;

use GridCP\Proxmox\Vm\Domain\Services\AVm;
use GridCP\Proxmox\Vm\Domain\Services\ISystemSo;

use Psr\Log\LoggerInterface;

class SoXConcrete extends AVm
{
    public function setVo(array $vm, array $params, $client, LoggerInterface       $loggers): ISystemSo
    {
        return new DebianXVmOk($vm, $params, $client, $loggers);
    }

    /*
            Otras acciones sobre Debian12
    
    */
}