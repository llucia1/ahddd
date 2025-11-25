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
use GridCP\Common\Domain\Bus\Query\QueryBus;

class DeleteVmToIp4ServiceTest extends TestCase
{
    private IVmRepository $vmRepository;
    private IVmIp4Repository $vmIpRepository;
    public LoggerInterface $logger;
    private QueryBus $queryBus;
    private DeleteVmToIp4Service $deleteVmToIp4Service;

    private VmUuid $vmUuid;
    private VmEntity $vmEntity;
    private VmIp4Entity $vmIp4Entity;
    private $faker;

    public function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->vmUuid = new VmUuid($this->faker->uuid);

        $this->vmRepository = $this->getMockBuilder(IVmRepository::class)->disableOriginalConstructor()->getMock();
        $this->vmIpRepository = $this->getMockBuilder(IVmIp4Repository::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBus = $this->createMock(QueryBus::class);

        $this->deleteVmToIp4Service = new DeleteVmToIp4Service($this->vmRepository, $this->vmIpRepository, $this->logger, $this->queryBus);

        $this->vmEntity = new VmEntity();
        $this->vmEntity->setId($this->faker->numberBetween(1, 100));
        $this->vmEntity->setUuid($this->vmUuid->value());

        $this->vmIp4Entity = new VmIp4Entity();
        $this->vmIp4Entity->setVm($this->vmEntity);
        $this->vmIp4Entity->setActive(true);
    }

    public function testDeleteVmToIp4Success(): void
    {
        $this->vmRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->vmUuid->value())
            ->willReturn($this->vmEntity);

        $this->vmIpRepository->expects($this->once())
            ->method('findByVmId')
            ->with($this->vmEntity->id())
            ->willReturn($this->vmIp4Entity);

        $this->vmIpRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (VmIp4Entity $entity) {
                return !$entity->isActive();
            }));

        $this->deleteVmToIp4Service->__invoke($this->vmUuid);
    }

    public function testDeleteVmToIp4VmNotFound(): void
    {
        $this->vmRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->vmUuid->value())
            ->willReturn(null);

        $this->expectException(VmNotFound::class);

        $this->deleteVmToIp4Service->__invoke($this->vmUuid);
    }

    public function testDeleteVmToIp4NoIpAssociated(): void
    {
        $this->vmRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->vmUuid->value())
            ->willReturn($this->vmEntity);

        $this->vmIpRepository->expects($this->once())
            ->method('findByVmId')
            ->with($this->vmEntity->id())
            ->willReturn(null);

        $this->expectException(NoIpAssociatedWithTheVm::class);

        $this->deleteVmToIp4Service->__invoke($this->vmUuid);
    }
}