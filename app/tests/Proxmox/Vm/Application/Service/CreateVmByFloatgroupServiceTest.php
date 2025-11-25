<?php
declare(strict_types=1);

namespace Proxmox\Vm\Application\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Application\Response\GetExceptionResponse;
use GridCP\Proxmox\Vm\Application\Service\CreateVmService;
use PHPUnit\Framework\TestCase;

use GridCP\Proxmox\Vm\Domain\VO\Vm;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetAllNodeByFloatgroupUuidQueried;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ListFloatGroupEmptyException;
use GridCP\Proxmox\Vm\Application\Helpers\IpsTrait;
use GridCP\Proxmox\Vm\Application\Service\AddVmToIp4Service;
use GridCP\Proxmox\Vm\Application\Service\GetAllFreeIpsOfOneNodeService;
use GridCP\Proxmox\Vm\Common\Service\PostVmByFloatgroupUuidService;
use GridCP\Proxmox\Vm\Common\Service\PostVmByNodeService;
use GridCP\Proxmox\Vm\Domain\Exception\floatgroupNotFoundException;
use GridCP\Proxmox\Vm\Domain\Exception\FreeIpNotFoundException;

use GridCP\Proxmox\Vm\Domain\VO\ClientUuid;
use GridCP\Proxmox\Vm\Domain\VO\FloatgroupUuid;
use GridCP\Proxmox\Vm\Domain\VO\IpVo;
use GridCP\Proxmox\Vm\Domain\VO\VmCpuCores;
use GridCP\Proxmox\Vm\Domain\VO\VmDiskSize;
use GridCP\Proxmox\Vm\Domain\VO\VmId;
use GridCP\Proxmox\Vm\Domain\VO\VmMask;
use GridCP\Proxmox\Vm\Domain\VO\VmMemory;
use GridCP\Proxmox\Vm\Domain\VO\VmName;
use GridCP\Proxmox\Vm\Domain\VO\VmNetBridge;
use GridCP\Proxmox\Vm\Domain\VO\VmNetGw;
use GridCP\Proxmox\Vm\Domain\VO\VmNetIp;
use GridCP\Proxmox\Vm\Domain\VO\VmNode;
use GridCP\Proxmox\Vm\Domain\VO\VmNodePveName;
use GridCP\Proxmox\Vm\Domain\VO\VmOs;
use GridCP\Proxmox\Vm\Domain\VO\VmPassword;
use GridCP\Proxmox\Vm\Domain\VO\VmStorage;
use GridCP\Proxmox\Vm\Domain\VO\VmTrafficLimit;
use GridCP\Proxmox\Vm\Domain\VO\VmUserName;
use GridCP\Proxmox\Vm\Domain\VO\VmUuid;
use Psr\Log\LoggerInterface;
use Tests\Proxmox\Vm\Application\Service\So\SoXConcrete;
use Tests\Proxmox\Vm\Application\Service\So\SoXConcreteError;
use Tests\Proxmox\Vm\Application\Service\VmHelper;

class CreateVmByFloatgroupServiceTest extends TestCase
{
    use VmHelper, IpsTrait;
    private $entityManager;
    private $createVmService;
    private $addVmToIp4Service;
    private $getAllFreeIpsOfOneNodeService;

    private $postVmByFloatgroupUuidService;
    private $logger;
    private $postVmByNodeService;
    private $vm;
    private $faker;

    private VmUuid $vmUuid;
    private ?ClientUuid $clientrUuid;
    private ?VmNode $vmGCPNode;
    private ?VmNodePveName $vmPVENode;
    private VmName $vmName;
    private ?VmNetIp $vmNetIp;
    private VmDiskSize $vmDiskSize;
    private VmStorage $vmStorage;
    private VmNetBridge $vmNetBridge;
    private VmCpuCores $vmCpucores;
    private VmMemory $vmMemory;
    private VmTrafficLimit $vmTrafficLimit;
    private ?VmNetGw $vmNetGw;
    private VmUserName $vmUserName;
    private VmPassword $vmPassword;
    private VmOs $vmOs;
    private ?FloatgroupUuid $floatgroupUuid;
    private ?VmMask $vmMask;
    private ?VmId $vmId;

    private Ip4Entity $ipEntity;
    private Vm $vmVo;

    private $queryBus;
    private array $ipResponse;


    protected function setUp(): void
    {

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->createVmService = $this->createMock(CreateVmService::class);
        $this->addVmToIp4Service = $this->createMock(AddVmToIp4Service::class);
        $this->getAllFreeIpsOfOneNodeService = $this->createMock(GetAllFreeIpsOfOneNodeService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBus = $this->createMock(QueryBus::class);
        $this->faker = FakerFactory::create();

        $this->postVmByFloatgroupUuidService = new PostVmByFloatgroupUuidService(
            $this->entityManager,
            $this->createVmService,
            $this->addVmToIp4Service,
            $this->getAllFreeIpsOfOneNodeService,
            $this->logger,
            $this->queryBus
        );









        $this->faker = FakerFactory::create();
        $this->vmUuid = new VmUuid($this->faker->uuid);
        $this->clientrUuid = new ClientUuid($this->faker->uuid);
        $this->vmGCPNode = new VmNode($this->faker->uuid);
        $this->vmPVENode = new VmNodePveName('Individual-X');
        $this->vmName = new VmName($this->faker->name);
        $this->vmNetIp = new VmNetIp($this->faker->ipv4);
        $this->vmDiskSize = new VmDiskSize($this->faker->randomNumber());
        $this->vmStorage = new VmStorage('nvme');
        $this->vmNetBridge = new VmNetBridge($this->faker->name);
        $this->vmCpucores = new VmCpuCores($this->faker->randomNumber());
        $this->vmMemory = new VmMemory($this->faker->randomNumber());
        $this->vmTrafficLimit = new VmTrafficLimit($this->faker->randomNumber());
        $this->vmNetGw = new VmNetGw('5.134.113.1');// NOSONAR
        $this->vmUserName = new VmUserName($this->faker->name);
        $this->vmPassword = new VmPassword($this->faker->password);
        $this->vmOs = new VmOs('Debian12');
        $this->floatgroupUuid = new FloatgroupUuid($this->faker->uuid);
        $this->vmMask = new VmMask('255.255.255.0');// NOSONAR
        $this->vmId = new VmId(3000);



        $this->vmVo = new Vm(
            $this->vmUuid,
            $this->clientrUuid,
            $this->vmGCPNode,
            $this->vmName,
            $this->vmNetIp,
            $this->vmDiskSize,
            $this->vmStorage,
            $this->vmNetBridge,
            $this->vmCpucores,
            $this->vmMemory,
            $this->vmTrafficLimit,
            $this->vmNetGw,
            $this->vmUserName,
            $this->vmPassword,
            $this->vmOs,
            $this->floatgroupUuid,
            $this->vmMask,
            $this->vmId
        );

        $floatroups = [
                        [
                            'id' => 1,
                            'uuid' => $this->vmVo->floatgroupUuid()->value(),
                            'name' => 'Ubrique',
                            'active' => 1,
                        ],
        ];
        $network = [
            'id' => 1,
            'uuid' => $this->faker->uuid(),
            'name' => $this->vmVo->netIp()->value(),
            'mask' => $this->vmVo->mask()->value(),
            'gateway' => $this->vmVo->netGw()->value(),
            'active' => 1,
            'floatGroups' => $floatroups,
        ];
        $this->ipResponse = [
            'ip' => [
                    'id' => 1,
                    'uuid' => $this->faker->uuid(),
                    'address' => $this->vmVo->netIp()->value(),
                    'active' => 1,
                    ],
            'network' => $network,
        ];
        $this->ipEntity = $this->ip4Entity($this->vmVo);
    }

    public function testCreateVmToFloatgroupSuccess(): void
    {
        $this->entityManager->expects($this->once())->method('beginTransaction');
        $this->entityManager->expects($this->once())->method('commit');
    
        $flArray = $this->FloatGroupArray(4);
        $floatgroupResponse = $this->FloatGroupResponse($flArray);
        $this->queryBus->expects($this->once())
            ->method('ask')
            ->with(new GetAllNodeByFloatgroupUuidQueried($this->vmVo->floatgroupUuid()->value()))
            ->willReturn($floatgroupResponse);
    
        $ipsFree = $this->arrayIp4Response(4);
        $this->getAllFreeIpsOfOneNodeService->expects($this->once())
            ->method('__invoke')
            ->willReturn($ipsFree);
    
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $this->createVmService->expects($this->once())
            ->method('__invoke')
            ->with($this->vmVo)
            ->willReturn($uuid);

        $result = $this->postVmByFloatgroupUuidService->__invoke($this->vmVo);
    
        $this->assertEquals($uuid, $result);
    }

    public function testCreateVmToFloatgroupFailsWhenNoNodes(): void
    {
        $this->entityManager->expects($this->once())->method('beginTransaction');
        $this->entityManager->expects($this->once())->method('rollback');


        $this->queryBus->expects($this->once())
            ->method('ask')
            ->with(new GetAllNodeByFloatgroupUuidQueried($this->vmVo->floatgroupUuid()->value()))
            ->willReturn( new GetExceptionResponse( new ListFloatGroupEmptyException() ) );

        $this->expectException(floatgroupNotFoundException::class);

        $this->postVmByFloatgroupUuidService->__invoke($this->vmVo);
    }
    
    /*
    php bin/phpunit tests/Proxmox/Vm/Application/Service/CreateVmByFloatgroupServiceTest.php
 
    */
}