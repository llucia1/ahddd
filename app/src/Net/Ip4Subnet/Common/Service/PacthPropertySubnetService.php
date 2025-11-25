<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Common\Service;


use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;
use GridCP\Net\Ip4Subnet\Application\Service\AddPropertySubnet;
use GridCP\Net\Ip4Subnet\Application\Service\DeletePropertySubnetService;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidUser;

use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;

final class PacthPropertySubnetService
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly AddPropertySubnet $addPropertySubnetService, private readonly DeletePropertySubnetService $deletePropertySubnetService, private readonly LoggerInterface $logger)
    {}
    public function __invoke(SubnetUuid $subnetUuidVo, ?UuidClient $clientUuidVo): void
    {
        $this->entityManager->beginTransaction();
        try {

            $this->logger->info('Start edit service Property Subnet: ' . $subnetUuidVo->value() );
            $this->logger->info('Start Delete Property Subnet with: ' . $subnetUuidVo->value());

            $this->deletePropertySubnetService->__invoke($subnetUuidVo);

            $this->logger->info('Start add new Property Subnet with: ' . $subnetUuidVo->value());

            $this->addPropertySubnetService->__invoke($subnetUuidVo, $clientUuidVo);

            $this->entityManager->commit();

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}
