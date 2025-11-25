<?php
declare(strict_types=1);
namespace Tests\Proxmox\Vm\Application\Service\So;

use GridCP\Proxmox\Vm\Domain\Services\AVm;
use GridCP\Proxmox\Vm\Domain\Services\ISystemSo;

class SoXConcreteError extends AVm
{
    public function setVo(array $vm, array $params, $client): ISystemSo
    {
        return new DebianXVmError($vm, $params, $client);
    }
    
    /*
            Otras acciones sobre Debian12
    
    */
}