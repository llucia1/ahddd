<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Application\Services;

use GridCP\Net\Ip4Network\Domain\Exception\NetworkNotExistException;
use GridCP\Net\Ip4Network\Domain\Repository\IIp4NetworkRepository;
use GridCP\Net\Ip4Network\Domain\Services\IPatchIpNetwork;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkPatch;

class PatchIPNetworkService implements IPatchIpNetwork
{
    public function __construct(
        readonly private IIp4NetworkRepository $ip4NetworkRepository,
    ){}

    public function __invoke(Ip4NetworkPatch $ip4Network, string $uuidIPNetwork ): void
    {
        $this->patchIPNetwork($ip4Network, $uuidIPNetwork);
    }

    public function patchIPNetwork(Ip4NetworkPatch $ip4Network, string $uuidIPNetwork):void
    {

        $networkEntity = $this->ip4NetworkRepository->getByUuid($uuidIPNetwork);
        if (!$networkEntity) {
            throw new NetworkNotExistException();
        }
        
        !is_null($ip4Network->Name()) ? $networkEntity->setName($ip4Network->Name()->value()) : null;
        !is_null($ip4Network->NameServer1()) ? $networkEntity->setNameServer1($ip4Network->NameServer1()->value()) : null;
        !is_null($ip4Network->NameServer2()) ? $networkEntity->setNameServer2($ip4Network->NameServer2()->value()) : null;
        !is_null($ip4Network->NameServer3()) ? $networkEntity->setNameServer3($ip4Network->NameServer3()->value()) : null;
        !is_null($ip4Network->NameServer4()) ? $networkEntity->setNameServer4($ip4Network->NameServer4()->value()) : null;
        !is_null($ip4Network->Priority()) ? $networkEntity->setPriority($ip4Network->Priority()->value()) : null;
        !is_null($ip4Network->SelectableByClient()) ? $networkEntity->setSelectableByClient($ip4Network->SelectableByClient()->value()) : null;
        !is_null($ip4Network->Free()) ? $networkEntity->setFree($ip4Network->Free()->value()) : null;
        !is_null($ip4Network->Netmask()) ? $networkEntity->setNetmask($ip4Network->Netmask()->value()) : null;
        !is_null($ip4Network->Gateway()) ? $networkEntity->setGateway($ip4Network->Gateway()->value()) : null;
        !is_null($ip4Network->Broadcast()) ? $networkEntity->setBroadcast($ip4Network->Broadcast()->value()) : null;
        !is_null($ip4Network->NoArp()) ? $networkEntity->setNoArp($ip4Network->NoArp()->value()) : null;
        !is_null($ip4Network->Rir()) ? $networkEntity->setRir($ip4Network->Rir()->value()) : null;
        !is_null($ip4Network->Active()) ? $networkEntity->setActive($ip4Network->Active()->value()) : null;
        
        $this->ip4NetworkRepository->save($networkEntity);
    }
}