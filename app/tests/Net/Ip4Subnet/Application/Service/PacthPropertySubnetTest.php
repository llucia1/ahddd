<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

namespace Tests\Net\Ip4Subnet\Application\Service;

use Doctrine\ORM\EntityManagerInterface;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetNoFound;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidUser;
use GridCP\Net\Ip4Subnet\Common\Service\PacthPropertySubnetService;
use GridCP\Net\Ip4Subnet\Application\Service\AddPropertySubnet;
use GridCP\Net\Ip4Subnet\Application\Service\DeletePropertySubnetService;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;

class PacthPropertySubnetTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AddPropertySubnet $addPropertySubnetService;
    private DeletePropertySubnetService $deletePropertySubnetService;
    private LoggerInterface $logger;
    private PacthPropertySubnetService $patchPropertySubnetService;
    private SubnetUuid $subnetUuid;
    private UuidClient $clientUuid;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->addPropertySubnetService = $this->createMock(AddPropertySubnet::class);
        $this->deletePropertySubnetService = $this->createMock(DeletePropertySubnetService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->patchPropertySubnetService = new PacthPropertySubnetService(
            $this->entityManager,
            $this->addPropertySubnetService,
            $this->deletePropertySubnetService,
            $this->logger
        );

        $this->subnetUuid = new SubnetUuid('ae901ebb-656f-44ff-b7d4-80bae65629c2');
        $this->clientUuid = new UuidClient('123e4567-e89b-12d3-a456-426614174000'); // Cambiado a UuidClient
    }

    public function testPatchPropertySubnetTransactionSuccess(): void
    {
        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->entityManager->expects($this->once())
            ->method('commit');

        $this->patchPropertySubnetService->__invoke($this->subnetUuid, $this->clientUuid);
    }

    public function testPatchPropertySubnetTransactionRollbackOnFailure(): void
    {
        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->entityManager->expects($this->never())
            ->method('commit');

        $this->entityManager->expects($this->once())
            ->method('rollback');

        $this->deletePropertySubnetService->expects($this->once())
            ->method('__invoke')
            ->with($this->subnetUuid)
            ->willThrowException(new SubnetNoFound($this->subnetUuid->value()));

        $this->expectException(SubnetNoFound::class);

        $this->patchPropertySubnetService->__invoke($this->subnetUuid, $this->clientUuid);
    }
}
    //     php bin/phpunit tests/Net/Ip4Subnet/Application/Service/PacthPropertySubnetTest.php
