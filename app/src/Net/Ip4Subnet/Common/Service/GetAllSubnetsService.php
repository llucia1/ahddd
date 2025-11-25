<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Common\Service;


use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;
use GridCP\Net\Ip4Subnet\Application\Help\SubnetsTrait;
use GridCP\Net\Ip4Subnet\Application\Response\FloatgroupResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Application\Response\OwnerResponse;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnets;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnetsOfOneClientByUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;

final class GetAllSubnetsService
{
    use SubnetsTrait;
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly GetAllSubnets $getAllSubnetsService, private readonly GetAllSubnetsOfOneClientByUuid $getAllSubnetsWithClientUUuidService, private readonly LoggerInterface $logger)
    {}
    public function __invoke(?UuidClient $uuidClient):Ip4SubnetsResponses
    {
        $this->entityManager->beginTransaction();
        try {

            if ($uuidClient) {
                $this->logger->info('Start All Subnet Of A With UuidClient: ' . $uuidClient->value() );
                $allSubnet = $this->getAllSubnetsWithClientUUuidService->__invoke($uuidClient);
            } else {
                $this->logger->info('Start All Subnet: ');
                $allSubnet = $this->getAllSubnetsService->__invoke( );
            }
            $this->entityManager->commit();
            return $this->subnetsClientResponses($allSubnet);
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}
