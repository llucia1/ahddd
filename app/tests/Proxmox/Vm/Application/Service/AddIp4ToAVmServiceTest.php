<?php
declare(strict_types=1);

namespace Proxmox\Vm\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Proxmox\Vm\Application\Service\AddVmToIp4Service;
use PHPUnit\Framework\TestCase;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetIp4EntityQueried;
use GridCP\Net\Ip4\Application\Response\Ip4EntityResponse;
use GridCP\Proxmox\Vm\Domain\Exception\IpNotFoundException;
use GridCP\Proxmox\Vm\Domain\Exception\IpWithVmDuplicates;
use GridCP\Proxmox\Vm\Domain\Exception\VmNotFound;
use GridCP\Proxmox\Vm\Domain\Exception\VmWthiIpDuplicates;
use GridCP\Proxmox\Vm\Domain\Repository\IVmRepository;
use GridCP\Proxmox\Vm\Domain\Repository\IVmIp4Repository;
use Psr\Log\LoggerInterface;
use GridCP\Proxmox\Vm\Domain\VO\VmUuid;
use GridCP\Proxmox\Vm\Domain\VO\IpVo;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmEntity;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmIp4Entity;

use Tests\Proxmox\Vm\Application\Service\VmHelper;
class AddIp4ToAVmServiceTest extends TestCase
{
    use VmHelper;
    private IVmRepository $vmRepository;
    private IVmIp4Repository $vmIpRepository;
    public LoggerInterface $logger;
    private QueryBus $queryBus;
    private AddVmToIp4Service $addVmToIp4Service;

    private VmUuid $vmUuid;
    private IpVo $ipVo;
    private VmEntity $vmEntity;
    private Ip4Entity $ipEntity;
    private $faker;

    public function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->vmUuid = new VmUuid($this->faker->uuid);
        $this->ipVo = new IpVo($this->faker->ipv4);

        $this->vmRepository = $this->getMockBuilder(IVmRepository::class)->disableOriginalConstructor()->getMock();
        $this->vmIpRepository = $this->getMockBuilder(IVmIp4Repository::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBus = $this->createMock(QueryBus::class);

        $this->addVmToIp4Service = new AddVmToIp4Service($this->vmRepository, $this->vmIpRepository, $this->logger, $this->queryBus);

        $this->vmEntity = $this->createVmEntity($this->vmUuid->value());
        $this->ipEntity = $this->createIp4Entity($this->ipVo->value());
    }

    private function prepareVmAndIp(): void
    {
        $this->vmRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->vmUuid->value())
            ->willReturn($this->vmEntity);

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->with(new GetIp4EntityQueried($this->ipVo->value()))
            ->willReturn(new Ip4EntityResponse($this->ipEntity));
    }

    public function testAddVmToIp4Success(): void
    {
        $this->prepareVmAndIp();

        $this->vmIpRepository->expects($this->once())
            ->method('findByIp4Id')
            ->with($this->ipEntity->getId())
            ->willReturn(null);

        $this->vmIpRepository->expects($this->once())
            ->method('findByVmId')
            ->with($this->vmEntity->id())
            ->willReturn(null);

        $this->vmIpRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(VmIp4Entity::class));

        $this->addVmToIp4Service->__invoke($this->vmUuid, $this->ipVo);
    }

    public function testAddVmToIp4VmNotFound(): void
    {
        $this->vmRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->vmUuid->value())
            ->willReturn(null);

        $this->expectException(VmNotFound::class);

        $this->addVmToIp4Service->__invoke($this->vmUuid, $this->ipVo);
    }

    public function testAddVmToIp4IpNotFound(): void
    {
        $this->vmRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->vmUuid->value())
            ->willReturn($this->vmEntity);

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->with(new GetIp4EntityQueried($this->ipVo->value()))
            ->willReturn(null);

        $this->expectException(IpNotFoundException::class);

        $this->addVmToIp4Service->__invoke($this->vmUuid, $this->ipVo);
    }

    public function testAddVmToIp4IpWithVmDuplicates(): void
    {
        $this->prepareVmAndIp();

        $this->vmIpRepository->expects($this->once())
            ->method('findByIp4Id')
            ->with($this->ipEntity->getId())
            ->willReturn(new VmIp4Entity());

        $this->expectException(IpWithVmDuplicates::class);

        $this->addVmToIp4Service->__invoke($this->vmUuid, $this->ipVo);
    }

    public function testAddVmToIp4VmWithIpDuplicates(): void
    {
        $this->prepareVmAndIp();

        $this->vmIpRepository->expects($this->once())
            ->method('findByIp4Id')
            ->with($this->ipEntity->getId())
            ->willReturn(null);

        $this->vmIpRepository->expects($this->once())
            ->method('findByVmId')
            ->with($this->vmEntity->id())
            ->willReturn(new VmIp4Entity());

        $this->expectException(VmWthiIpDuplicates::class);

        $this->addVmToIp4Service->__invoke($this->vmUuid, $this->ipVo);
    }
    

    /*
    php bin/phpunit tests/Proxmox/Vm/Application/Service/AddIp4ToAVmServiceTest.php
 
    */
}