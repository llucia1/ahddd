<?php
declare(strict_types=1);
namespace Tests\Proxmox\Vm\Application\Service\So;



use GridCP\Proxmox\Common\Infrastructure\ProxmoxClientService;
use GridCP\Proxmox\Vm\Application\Helpers\FunctionsProxmoxVmTrait;
use GridCP\Proxmox\Vm\Domain\Exception\SystemSoNotExistException;
use GridCP\Proxmox\Vm\Domain\Services\ISystemSo;

class DebianXVmOk implements ISystemSo
{
    use FunctionsProxmoxVmTrait;
    private array $parameters = [];
    
    public ProxmoxClientService $proxmoxClientService;

    public function __construct(private array $vm, array $params, $client)
    {
        $this->vm = $vm;
        $this->parameters = $params;
        $this->proxmoxClientService = $client;
    }

    public function create(): array
    {
        if ( empty($this->parameters))
        {
             return new SystemSoNotExistException();
        }
        return[
            'create' => 'ok',
            'error' => null
           ];

    }
}

