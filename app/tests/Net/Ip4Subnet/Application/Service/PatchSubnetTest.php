<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

namespace Tests\Net\Ip4Subnet\Application\Service;

use Doctrine\ORM\EntityManagerInterface;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetArrayResponse;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetEntityResponse;
use GridCP\Net\Ip4Subnet\Application\Service\AddPropertySubnet;
use GridCP\Net\Ip4Subnet\Common\Service\PatchSubnetService;
use GridCP\Net\Ip4Subnet\Application\Service\GetIp4SubnetService;
use GridCP\Net\Ip4Subnet\Application\Service\CreateIp4Subnet;
use GridCP\Net\Ip4Subnet\Application\Service\DeleteSubnetService;
use GridCP\Net\Ip4Subnet\Domain\VO\Ip4SubnetPacthVo;
use GridCP\Net\Ip4Subnet\Domain\VO\Ip4SubnetVo;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetIP;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidFloatgroup;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;


class PatchSubnetTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private GetIp4SubnetService $getIp4SubnetService;
    private CreateIp4Subnet $addSubnetService;
    private DeleteSubnetService $deleteSubnetService;
    private LoggerInterface $logger;
    private PatchSubnetService $patchSubnetService;
    private Ip4SubnetPacthVo $subnetVo;
    private AddPropertySubnet $addPropertySubnetService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->getIp4SubnetService = $this->createMock(GetIp4SubnetService::class);
        $this->addSubnetService = $this->createMock(CreateIp4Subnet::class);
        $this->deleteSubnetService = $this->createMock(DeleteSubnetService::class);
        $this->addPropertySubnetService = $this->createMock(AddPropertySubnet::class);

        $this->logger = $this->createMock(LoggerInterface::class);

        $this->patchSubnetService = new PatchSubnetService(
            $this->entityManager,
            $this->getIp4SubnetService,
            $this->addSubnetService,
            $this->deleteSubnetService,
            $this->addPropertySubnetService,
            $this->logger
        );

        $subnetUuid = new SubnetUuid('ae901ebb-656f-44ff-b7d4-80bae65629c2');
        $floatgroupUuid = new UuidFloatgroup('123e4567-e89b-12d3-a456-426614174000');
        $this->subnetVo = new Ip4SubnetPacthVo($subnetUuid, $floatgroupUuid, new SubnetMask(24), new SubnetIP('192.168.1.0')); // NOSONAR
    }

    /**
     * @dataProvider providePatchSubnetScenarios
     */
    public function testPatchSubnetTransaction($subnetExists, $shouldThrowException, $isAdmin, $expectedException = null): void
    {
        $validSubnetUuid = '123e4567-e89b-12d3-a456-426614174000'; // UUID válido
        $validClientUuid = 'e9a3baa1-1ca4-4c0d-8a0b-cb877491a486'; // UUID válido para el cliente
        $subnetArrayResponse = $subnetExists ? new SubnetArrayResponse([
            'uuid' => $this->subnetVo->subnetUUid()->value(),
            'uuid_floatgroup' => $this->subnetVo->subnetUUidFloatgroup()->value(),
            'mask' => 24,
            'ip' => '192.168.1.0',// NOSONAR
            'client_uuid' => $validClientUuid,
        ]) : null;
    
        $this->entityManager->expects($this->once())
            ->method('beginTransaction');
    
        if ($subnetExists) {
            $this->getIp4SubnetService->expects($this->once())
                ->method('__invoke')
                ->with($this->subnetVo->subnetUUid())
                ->willReturn($subnetArrayResponse);
    
            $this->deleteSubnetService->expects($this->once())
                ->method('__invoke')
                ->with($this->subnetVo->subnetUUid());
    
            $this->addSubnetService->expects($this->once())
                ->method('__invoke')
                ->with($this->isInstanceOf(Ip4SubnetVo::class))
                ->willReturn($validSubnetUuid);
    
            $this->addPropertySubnetService->expects($this->once())
                ->method('__invoke')
                ->with(
                    $this->isInstanceOf(SubnetUuid::class),
                    $this->isInstanceOf(UuidClient::class)
                );
        } else {
            $this->getIp4SubnetService->expects($this->once())
                ->method('__invoke')
                ->with($this->subnetVo->subnetUUid())
                ->willThrowException(new HttpException(500, 'Error retrieving subnet'));
        }
    
        if ($shouldThrowException) {
            $this->entityManager->expects($this->once())
                ->method('rollback');
            $this->expectException($expectedException);
        } else {
            $this->entityManager->expects($this->once())
                ->method('commit');
        }
    
        $this->patchSubnetService->__invoke($this->subnetVo, $isAdmin);
    }
    public function providePatchSubnetScenarios(): array
    {
        return [
            'Success as admin' => [true, false, true],
            'Success as non-admin' => [true, false, false],
            'Failure - Subnet Not Found' => [false, true, true, HttpException::class]
        ];
    }
}
//     php bin/phpunit tests/Net/Ip4Subnet/Application/Service/PatchSubnetTest.php