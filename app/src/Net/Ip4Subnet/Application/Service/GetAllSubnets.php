<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;

use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsNoFound;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use Psr\Log\LoggerInterface;

class GetAllSubnets
{
    public function __construct(
        private readonly IIp4SubnetRepository $subnetRepository,
        public LoggerInterface       $logger
    ){ }

    public function __invoke(?string $clientUuid = null): array
    {
        $this->logger->info("Service - Start Get All Subnets");
        $allSubnet = $this->subnetRepository->getAllWithRelation();
        if (!$allSubnet) {
            throw new SubnetsNoFound();
        }
        return $allSubnet;
    }
}