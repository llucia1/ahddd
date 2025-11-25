<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;

use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsNoFound;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use GridCP\Security\Common\Infrastructure\DB\MySQL\Entity\AuthEntity;
use Psr\Log\LoggerInterface;

class GetAllSubnetsOfOneClientByUuid
{
    public function __construct(
        private readonly IIp4SubnetRepository $subnetRepository,
        public LoggerInterface       $logger
    ){ }

    public function __invoke(?UuidClient $uuidclient): array
    {
        $this->logger->info("Service - Start Get All Subnets Of One Client By Uuid");
        $clientUuid = ($uuidclient) ? $uuidclient->getValueUuid() : null;

        $this->logger->info("Client found ", ['uuid' => $clientUuid]);
        $allSubnet = $this->subnetRepository->getAllWithRelationsByUuidClient( $clientUuid );
        if (empty($allSubnet)) {
            throw new SubnetsNoFound();
        }
        
        return $allSubnet;
    }
}