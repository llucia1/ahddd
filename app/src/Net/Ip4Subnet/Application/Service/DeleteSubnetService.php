<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;

use Exception;

use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetNoFound;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use Psr\Log\LoggerInterface;

class DeleteSubnetService
{
    public function __construct(
        private readonly Ip4SubnetRepository $ip4SubnetRepository,
        private readonly IIp4SubnetOwnerRepository $propertySubnetRepository,
        public LoggerInterface       $logger
    )    {    }

    public function __invoke(SubnetUuid $subnetUuid): void
    {
        $this->logger->info("Service - Start Delete Property Subnet");

        $subnet = $this->ip4SubnetRepository->findByUuid( $subnetUuid->value() );
        if (!$subnet) {
            throw new SubnetNoFound($subnetUuid->value());
        }
        $subnet->setActive(false);
        try {
            $this->ip4SubnetRepository->save($subnet);
            $this->deletePropertySubnet($subnet->getUuid());
        } catch (Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $e->getMessage());
        }
    }

    private function deletePropertySubnet(?string $subnetUuid): void
    {

        $propertySubnet = $this->propertySubnetRepository->findBySubnetUuid( $subnetUuid );
        if ($propertySubnet) {
            $propertySubnet->setActive(false);
            $this->propertySubnetRepository->save($propertySubnet);
        }
    }
}