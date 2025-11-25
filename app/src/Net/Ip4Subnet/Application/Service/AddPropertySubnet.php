<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;



use GridCP\Client\Application\Cqrs\Queries\GetClientEntityByUuidQuerie;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetDuplicated;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetNoFound;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Ip4Subnet\Domain\Exception\ClientNoFound;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use Psr\Log\LoggerInterface;

class AddPropertySubnet
{
    public function __construct(
        private readonly Ip4SubnetRepository $ip4SubnetRepository,
        private readonly IIp4SubnetOwnerRepository $propertySubnetRepository,
        public LoggerInterface       $logger,
        private QueryBus             $queryBus,
    )
    {
    }

    public function __invoke(SubnetUuid $subnetUuid, ?UuidClient $uuidclient): string
    {
        $this->logger->info("Service - Start POST Add Property Subnet");
        $clientUuid = $uuidclient ? $uuidclient->getValueUuid() : null;
        if ($clientUuid) {
            $this->existClient($clientUuid);
        }

        $this->logger->info("Client found ", ['uuid' => $clientUuid]);
        $subnet = $this->ip4SubnetRepository->findByUuid( $subnetUuid->value() );
        if (!$subnet) {
            throw new SubnetNoFound($subnetUuid->value());
        }
        $this->logger->info("Subnet found ", ['uuid' => $subnet->getUuid()]);

        $propertySubnetDuplicated = $this->propertySubnetRepository->findBySubnetUuid( $subnet->getUuid() );
        if ($propertySubnetDuplicated) {
            throw new SubnetDuplicated($subnetUuid->value());
        }
        $propertySubnet = new Ip4SubnetOwnerEntity();
        $propertySubnet->setUuid(SubnetUuid::random()->value());
        $propertySubnet->setSubnet($subnet);
        $propertySubnet->setClientUuid($clientUuid);
        $propertySubnet->setActive(true);
        $this->logger->info("PropertyEntity to save:", [
                                                        'Uuid' => $propertySubnet->getUuid(),
                                                        'Subnet(IP)' => $propertySubnet->getSubnet()->getIp(),
                                                        'Client(uuid)' => $propertySubnet->getClientUuid()
                                                       ]);
               
        $this->propertySubnetRepository->save($propertySubnet);
        $this->logger->info("PropertySubnet successfully saved.");
        return $propertySubnet->getUuid();
    }

    private function existClient(string $clientUuid): void
    {
        $this->logger->info("Service - Start Event Get Client By Uuid = " . $clientUuid);
        try {
            $clientExist = $this->queryBus->ask(new GetClientEntityByUuidQuerie($clientUuid));
            if (!$clientExist->get()) {
                throw new ClientNoFound($clientUuid);
            }
        }catch(\Exception $ex){
            throw new ClientNoFound($clientUuid);
        }
    }
}