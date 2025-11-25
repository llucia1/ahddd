<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;

use Exception;
use GridCP\Net\Ip4Subnet\Application\Help\SubnetsTrait;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetNoFound;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

use GridCP\Net\Ip4Subnet\Domain\Exception\PropertySubnetNotFound;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use Psr\Log\LoggerInterface;

class DeletePropertySubnetService
{
    use SubnetsTrait;
    public function __construct(
        private readonly Ip4SubnetRepository $ip4SubnetRepository,
        private readonly IIp4SubnetOwnerRepository $propertySubnetRepository,
        public LoggerInterface       $logger
    )
    {
    }

    public function __invoke(SubnetUuid $subnetUuid): void
    {
        $this->logger->info("Service - Start Delete Property Subnet");
        
        $subnet = $this->ip4SubnetRepository->findByUuid( $subnetUuid->value() );
        if (!$subnet) {
            throw new SubnetNoFound($subnetUuid->value());
        }
        $propertySubnet = $this->getProperty($subnet->getPropertySubnet());
        if (!$propertySubnet) {
            throw new PropertySubnetNotFound();
        }
        
        
        $propertySubnet->setActive(false);
        try {
            $this->propertySubnetRepository->save($propertySubnet);
        } catch (Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $e->getMessage());
        }
    }
}