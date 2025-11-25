<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;

use GridCP\Net\Common\Application\Helpers\CalcIps;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetArrayResponse;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetNoFound;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;



use Psr\Log\LoggerInterface;

class GetIp4SubnetService
{
    use CalcIps;
    public function __construct(
        private readonly Ip4SubnetRepository $ip4SubnetRepository,
        public LoggerInterface       $logger
    )
    {
    }

    public function __invoke(SubnetUuid $subnetUuid): SubnetArrayResponse
    {
        $this->logger->info("Service - Start Get Subnet With uuid: " . $subnetUuid->value());

        $subnetEntity = $this->ip4SubnetRepository->findByUuidWithRelations($subnetUuid->value());
        if (!$subnetEntity) {
            throw new SubnetNoFound($subnetUuid->value());
        }
        

        return new SubnetArrayResponse($subnetEntity);
    }
}