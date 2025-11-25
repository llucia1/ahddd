<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;

use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;

use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsNoFound;
use Psr\Log\LoggerInterface;

class GetAllSubnetsByClientByUuid
{
    public function __construct(
        private readonly IIp4SubnetOwnerRepository $propertySubnetRepository,
        public LoggerInterface       $logger
    ){ }

    public function __invoke(?UuidClient $uuidclient)
    {
        $this->logger->info("Service - Start Get All Subnets Of One Client By Uuid");

        $clientUuid = $uuidclient ? $uuidclient->getValueUuid() : null;

        $this->logger->info("Client found ", ['uuid' => $clientUuid]);
        $allSubnet = $this->propertySubnetRepository->getAllSubnetsByClientUuid( $clientUuid );
        if (!$allSubnet) {
            throw new SubnetsNoFound();
        }
        
        return $allSubnet;
    }
}