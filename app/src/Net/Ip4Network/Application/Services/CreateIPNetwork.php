<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Application\Services;

use GridCP\Net\Ip4Network\Domain\Exception\IP4NetworkDuplicated;

use GridCP\Net\Ip4Network\Domain\Repository\IIp4NetworkRepository;
use GridCP\Net\Ip4Network\Domain\Services\ICreateIpNetwork;
use GridCP\Net\Ip4Network\Domain\VO\Ip4Network;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;

class CreateIPNetwork implements ICreateIpNetwork
{
    public function __construct(
        readonly private IIp4NetworkRepository $ip4NetworkRepository,
    )
    {
    }

    public function __invoke(Ip4Network $ip4Network ): string
    {
        return $this->createIPNetwork($ip4Network);
    }

    public function createIPNetwork(Ip4Network $ip4Network):string
    {

        $existingNetwork = $this->ip4NetworkRepository->getByName($ip4Network->Name()->value());

        if ($existingNetwork) {
            throw new IP4NetworkDuplicated($existingNetwork->getName());
        }

        $networkEntity = new Ip4NetworkEntity();
        $networkEntity->setUuid($ip4Network->Uuid()->value());
        $networkEntity->setName($ip4Network->Name()->value());
        $networkEntity->setNameServer1($ip4Network->NameServer1()->value());
        $networkEntity->setNameServer2($ip4Network->NameServer2()->value());
        $networkEntity->setNameServer3( ($ip4Network->NameServer3())?  $ip4Network->NameServer3()->value() : null);
        $networkEntity->setNameServer4( ($ip4Network->NameServer4())? $ip4Network->NameServer4()->value() : null);
        $networkEntity->setPriority($ip4Network->Priority()->value());
        $networkEntity->setNetmask($ip4Network->Netmask()->value());
        $networkEntity->setGateway($ip4Network->Gateway()->value());
        $networkEntity->setBroadcast($ip4Network->Broadcast()->value());
        $networkEntity->setRir($ip4Network->Rir()->value());
        $networkEntity->setNoArp($ip4Network->NoArp()->value());
        $this->ip4NetworkRepository->save($networkEntity);
        return $networkEntity->getUuid();
    }
}