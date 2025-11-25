<?php

namespace Tests\Proxmox\Vm\Application\Service;

use PHPUnit\Framework\TestCase;
use GridCP\Proxmox\Vm\Domain\Repository\IVmRepository;
use GridCP\Proxmox\Vm\Domain\Repository\IVmIp4Repository;
use GridCP\Proxmox\Vm\Application\Service\DeleteVmToIp4Service;
use GridCP\Proxmox\Vm\Domain\VO\VmUuid;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmEntity;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmIp4Entity;
use GridCP\Proxmox\Vm\Domain\Exception\VmNotFound;
use GridCP\Proxmox\Vm\Domain\Exception\NoIpAssociatedWithTheVm;
use Psr\Log\LoggerInterface;
use Faker\Factory as FakerFactory;
use Doctrine\ORM\EntityManagerInterface;
use GridCP\Proxmox\Vm\Application\Service\AddVmToIp4Service;
use GridCP\Proxmox\Vm\Common\Service\PacthVmToIp4Service;
use GridCP\Proxmox\Vm\Domain\VO\IpVo;

class PatchVmToIp4ServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AddVmToIp4Service $addVmToIp4Service;
    private DeleteVmToIp4Service $deleteVmToIp4Service;
    private LoggerInterface $logger;
    private PacthVmToIp4Service $patchVmToIp4Service;
    private VmUuid $vmUuid;
    private IpVo $ip;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->addVmToIp4Service = $this->createMock(AddVmToIp4Service::class);
        $this->deleteVmToIp4Service = $this->createMock(DeleteVmToIp4Service::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->patchVmToIp4Service = new PacthVmToIp4Service(
            $this->entityManager,
            $this->addVmToIp4Service,
            $this->deleteVmToIp4Service,
            $this->logger
        );

        $this->vmUuid = new VmUuid('ae901ebb-656f-44ff-b7d4-80bae65629c2');
        $this->ip = new IpVo('192.168.1.1');
    }

    public function testPatchVmToIp4Success(): void
    {
        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->deleteVmToIp4Service->expects($this->once())
            ->method('__invoke')
            ->with($this->vmUuid);

        $this->addVmToIp4Service->expects($this->once())
            ->method('__invoke')
            ->with($this->vmUuid, $this->ip);

        $this->entityManager->expects($this->once())
            ->method('commit');

        $this->patchVmToIp4Service->__invoke($this->vmUuid, $this->ip);
    }

    public function testPatchVmToIp4Failure(): void
    {
        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->deleteVmToIp4Service->expects($this->once())
            ->method('__invoke')
            ->will($this->throwException(new \Exception('Deletion failed')));

        $this->entityManager->expects($this->once())
            ->method('rollback');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Deletion failed');

        $this->patchVmToIp4Service->__invoke($this->vmUuid, $this->ip);
    }

    public function testPatchVmToIp4CommitFailure(): void
    {
        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->deleteVmToIp4Service->expects($this->once())
            ->method('__invoke')
            ->with($this->vmUuid);

        $this->addVmToIp4Service->expects($this->once())
            ->method('__invoke')
            ->with($this->vmUuid, $this->ip);

        $this->entityManager->expects($this->once())
            ->method('commit')
            ->will($this->throwException(new \Exception('Commit failed')));

        $this->entityManager->expects($this->once())
            ->method('rollback');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Commit failed');

        $this->patchVmToIp4Service->__invoke($this->vmUuid, $this->ip);
    }
}
// php bin/phpunit tests/Proxmox/Vm/Application/Service/PatchVmToIp4ServiceTest.php