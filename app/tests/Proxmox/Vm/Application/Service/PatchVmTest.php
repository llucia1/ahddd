<?php
declare(strict_types=1);

namespace Proxmox\Vm\Application\Service;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Common\Infrastructure\OpenSSL\OpenSSLService;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeEntity;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeFloatGroupEntity;
use GridCP\Proxmox\Common\Infrastructure\ProxmoxClientService;
use GridCP\Proxmox\Storage\Application\Cqrs\Queries\GetPromoxStorageQueried;
use GridCP\Proxmox\Storage\Application\Response\StorageResponses;
use GridCP\Proxmox\Vm\Domain\VO\Nodo\Cpu\CpuName;
use PHPUnit\Framework\TestCase;
use GridCP\Proxmox\Vm\Application\Helpers\ProxmoxVmFunctions;
use GridCP\Proxmox\Vm\Application\Response\CpuEntityResponse;
use GridCP\Proxmox\Vm\Application\Response\DeleteVmResponse;
use GridCP\Proxmox\Vm\Application\Response\NodeEntityResponse;
use GridCP\Proxmox\Vm\Application\Response\VmEntityResponse;
use GridCP\Proxmox\Vm\Application\Service\GetVmByUuidservice;
use GridCP\Proxmox\Vm\Application\Service\PatchVmService;
use GridCP\Proxmox\Vm\Domain\VO\Vm;
use GridCP\Proxmox\Vm\Domain\Exception\VmNotExitsException;
use GridCP\Proxmox\Vm\Domain\Exception\VmNotFound;
use GridCP\Proxmox\Vm\Domain\Repository\IVmRepository;
use GridCP\Proxmox\Vm\Domain\Services\IDeleteVmService;
use GridCP\Proxmox\Vm\Domain\VO\FloatgroupUuid;
use GridCP\Proxmox\Vm\Domain\VO\Nodo\NodeId;
use GridCP\Proxmox\Vm\Domain\VO\VmActive;
use GridCP\Proxmox\Vm\Domain\VO\VmCores;
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
use GridCP\Proxmox\Vm\Domain\VO\VmPatch;
use GridCP\Proxmox\Vm\Domain\VO\VmStorage;
use GridCP\Proxmox\Vm\Domain\VO\VmTrafficLimit;
use GridCP\Proxmox\Vm\Domain\VO\VmUserName;
use GridCP\Proxmox\Vm\Domain\VO\VmUuid;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmEntity;
use GridCP\Proxmox_Client\Nodes\Domain\Responses\NodeResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use GridCP\Proxmox_Client\Nodes\Domain\Responses\NodesResponse as NodesResponseProxmox;
use GridCP\Proxmox_Client\Storages\Domain\Responses\StorageResponse;
class PatchVmTest extends TestCase
{
    protected Generator $faker;
    protected IVmRepository $vmRepository;
    protected LoggerInterface $logger;
    protected QueryBus $queryBusMock;
    protected ProxmoxClientService $ProxmoxClientService;
    protected EntityManagerInterface $entityManager;
    protected IDeleteVmService $deleteVmService;
    protected $openSSLService;

    protected  $patchVmService;
    protected VmPatch $vmPatch;
    protected VmEntity $vmEntity;
    protected Vm $vmVo;
    protected array $resultFindVm;
    protected $NodesResponseProxmox;
    protected $getStorageProxmox;

    use ProxmoxVmFunctions;
    private const DEFAULT_VM_IP = '192.168.1.8';// NOSONAR
    private const DEFAULT_VM_IP2 = '192.168.1.0';// NOSONAR
    private const DEFAULT_VM_IP3 = '192.168.0.1';// NOSONAR
    private const DEFAULT_VM_IP_MASK = '255.255.255.0';// NOSONAR
    public function setUp(): void
    {
        $this->vmRepository = $this->getMockBuilder(IVmRepository::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBusMock = $this->createMock(QueryBus::class);
        $this->ProxmoxClientService = $this->createMock(ProxmoxClientService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->deleteVmService = $this->createMock(IDeleteVmService::class);
        $this->faker = FakerFactory::create();

    $containerBagMock = $this->createMock(ContainerBagInterface::class);
    $containerBagMock->method('get')
        ->willReturnMap([
            ['GridCP.CIPHERING', 'AES-256-CBC'],
            ['GridCP.PII_KEY', '12345678901234567890123456789012'],
        ]);
    $this->openSSLService = new OpenSSLService($containerBagMock);
    
    $uuid = $this->faker->uuid();
        $vmEntity = new VmEntity();
        $vmEntity->setId(1);
        $vmEntity->setUuid($uuid);
        $vmEntity->setName('nodeVpeName');
        $vmEntity->setCores($this->faker->randomNumber(2));
        $vmEntity->setOs('Debian 12');
        $vmEntity->setDiskSize('20G');
        $vmEntity->setIp(self::DEFAULT_VM_IP);
        $vmEntity->setStorage('nvme');
        $vmEntity->setBridge('bridge0');
        $vmEntity->setTrafficLimit(8000);
        $vmEntity->setGateway(self::DEFAULT_VM_IP2);
        $vmEntity->setMemory(2048);
        $vmEntity->setUserName('user');
        $vmEntity->setPassword('password');
        $vmEntity->setVmid(288);
        
        $vmEntity->setActive(true); 

        $vmEntity->setPassword($this->openSSLService->encrypt('test-password')); 

        $this->vmEntity = $vmEntity;

        $this->patchVmService = new PatchVmService($this->vmRepository, $this->logger, $this->queryBusMock, $this->ProxmoxClientService, $this->entityManager, $this->deleteVmService, $this->openSSLService );

        $uuidVm = new VmUuid($vmEntity->uuid());
        $name = new VmName($vmEntity->name());
        $cores = new VmCpuCores(1);
        $this->vmPatch = new VmPatch( $uuidVm, null, null, $name, null, null, null, null, $cores, null, null, null, null, null, null, null, null, null, null );        

        $nodeEntity = $this->getNode();

        $floatgroup = new Ip4FloatGroupEntity();
        $floatgroup->setId(1);
        $floatgroup->setUuid($this->faker->uuid());
        $floatgroup->setName('floatgroup-name');
        $floatgroup->setActive(true);

        $floatgroupNode = new NodeFloatGroupEntity();
        $floatgroupNode->setId(1);
        $floatgroupNode->setNode($nodeEntity);
        $floatgroupNode->setFloatGroup($floatgroup);

        $vmEntity->setIdNode($nodeEntity->getId());
        $vmEntity->setNodeEntity($nodeEntity);

        $this->vmVo = new Vm(
            new VmUuid( $vmEntity->uuid() ),
            null,
            new VmNode($nodeEntity->getGcpName()),
            new VmName($name->value()),
            new VmNetIp( $vmEntity->getIp() ),
            new VmDiskSize($vmEntity->getDiskSize()),
            new VmStorage( $vmEntity->storage() ),
            new VmNetBridge($vmEntity->bridge()),
            new VmCpuCores($cores->value()),
            new VmMemory($vmEntity->getMemory()),
            new VmTrafficLimit(8000),
            new VmNetGw($vmEntity->getGateway()),
            new VmUserName($vmEntity->getUsername()),
            new VmPassword($vmEntity->getPassword()),
            new VmOs('Debian 12'),
            new FloatgroupUuid($floatgroup->getUuid()),
            new VmMask(self::DEFAULT_VM_IP_MASK),
            new VmId($vmEntity->getVmId())
        );
        $patchVmService = $this->getMockBuilder(PatchVmService::class)
            ->onlyMethods(['reinstallVm', 'updateVM'])
            ->disableOriginalConstructor()
            ->getMock();
            $this->resultFindVm = [
                'vm' => $this->vmEntity,
                'node' => $nodeEntity
            ];
            $this->NodesResponseProxmox = [
                0 =>
                new NodesResponseProxmox(
                    ...[
                        new NodeResponse(
                            'online',
                            'info',
                            $nodeEntity->getUuid(),
                            'ssl-fingerprint',
                            16000000000,
                            50000000000,
                            1000000,
                            8000000000,
                            $nodeEntity->getGcpName(),
                            2.5,
                            16000000000,
                            'qemu',
                            50000000000
                        ),
                    ]
                ),
                1 =>
                new NodeEntityResponse(
                    $nodeEntity->getId(),
                    $nodeEntity->getUuid(),
                    $nodeEntity->getGcpName(),
                    $nodeEntity->getPveName(),
                    $nodeEntity->getPveHostName(),
                    $nodeEntity->getPveUserName(),
                    '12345678',
                    $nodeEntity->getPveRealm(),
                    $nodeEntity->getPvePort(),
                    $nodeEntity->getPveIp(),
                    $nodeEntity->getSshPort(),
                    $nodeEntity->getTimezone(),
                    $nodeEntity->getKeyboard(),
                    $nodeEntity->getDisplay(),
                    $nodeEntity->getStorage(),
                    $nodeEntity->getStorageIso(),
                    $nodeEntity->getStorageImage(),
                    $nodeEntity->getStorageBackup(),
                    $nodeEntity->getNetworkInterface(),
                    null
                )

            ];
            $this->vmVo->setPveNode( $nodeEntity->getPveName() );
            $this->getStorageProxmox = new StorageResponses(
                [
                    new StorageResponse(
                                        type: 'lvmthin',
                                        used: 7114998782,
                                        avail: 1817243663362,
                                        total: 1824358662144,
                                        enabled: true,
                                        storage: $vmEntity->storage(),
                                        used_fraction: 0.0039,
                                        content: ['rootdir', 'images'],
                                        active: true,
                                        shared: false
                                    )

                ]
            );
        $this->patchVmService = $this->getMockBuilder(PatchVmService::class)
            ->onlyMethods(['reinstallVm', 'updateVM'])
            ->setConstructorArgs([
                $this->vmRepository,
                $this->logger,
                $this->queryBusMock,
                $this->ProxmoxClientService,
                $this->entityManager,
                $this->deleteVmService,
                $this->openSSLService
            ])
            ->getMock();
    }
    private function getNode():NodeEntity
    {
        $nodeEntity = new NodeEntity();
        $nodeEntity->setId (1);
        $nodeEntity->setUuid($this->faker->uuid());
        $nodeEntity->setGcpName('nodeGcpName');
        $nodeEntity->setPveName('nodeVpeName');
        $nodeEntity->setPveHostName( 'node-vpe-hostname');
        $nodeEntity->setPveUserName('node-vpe-username');
        $nodeEntity->setPvePassword('node-vpe-password');
        $nodeEntity->setPveRealm('realm');
        $nodeEntity->setPvePort(8006);
        $nodeEntity->setPveIp(self::DEFAULT_VM_IP3);
        $nodeEntity->setSshPort(22);
        $nodeEntity->setTimezone('es');
        $nodeEntity->setKeyboard('es');
        $nodeEntity->setDisplay('es');
        $nodeEntity->setStorage('local');
        $nodeEntity->setStorageIso('local-iso');
        $nodeEntity->setStorageImage('local-image');
        $nodeEntity->setStorageBackup('local-backup');
        $nodeEntity->setNetworkInterface('eth0');
        $nodeEntity->setCpu(    'Intel Xeon E5-2670 v3');
        return $nodeEntity;
    }
    private function configMock()
    {
            $this->vmRepository->method('findWithNodeByUuid')
                ->willReturn($this->resultFindVm);
            $this->deleteVmService->method('delete')
                ->willReturn(new DeleteVmResponse($this->faker->uuid()));
            $this->ProxmoxClientService->method('getNodesWithAuth')
                ->willReturn($this->NodesResponseProxmox);
            $this->queryBusMock->method('ask')
                ->willReturn($this->getStorageProxmox);
    }
    private function getPatchService()
    {
        return $this->getMockBuilder(PatchVmService::class)
                ->onlyMethods(['reinstallVm', 'updateVM'])
                ->setConstructorArgs([
                    $this->vmRepository,
                    $this->logger,
                    $this->queryBusMock,
                    $this->ProxmoxClientService,
                    $this->entityManager,
                    $this->deleteVmService,
                    $this->openSSLService
                ])
                ->getMock();
    }
    public function testVmUpdateOK(): void
    {
           $this->configMock();
            $this->vmVo->setOs(null);
            $patchVmService = $this->getPatchService();
            $patchVmService->expects($this->once())
                ->method('updateVM')
                ->with(
                    $this->callback(fn($actualVm) => 
                        $actualVm instanceof Vm &&
                        $actualVm->uuid()->value() === $this->vmVo->uuid()->value()
                    ),
                    $this->equalTo($this->vmPatch)
                )
                ->willReturn($this->vmVo->uuid()->value());
            $patchVmService->expects($this->never())
                ->method('reinstallVm');
            $patchVmService->__invoke($this->vmPatch);
            $this->assertTrue(true);
    }
    public function testVmUpdateInstallOK(): void
    {
        $this->configMock();
        $this->vmVo->setOs(new VmOs('Debian 12'));
        $patchVmService = $this->getPatchService();
        $patchVmService->expects($this->once())
            ->method('updateVM')
            ->with(
                $this->callback(fn($actualVm) => 
                    $actualVm instanceof Vm &&
                    $actualVm->uuid()->value() === $this->vmVo->uuid()->value()
                )
            )
            ->willReturn($this->vmVo->uuid()->value());
        $patchVmService->expects($this->never())
            ->method('reinstallVm');
        $patchVmService->__invoke($this->vmPatch);
        $this->assertTrue(true);
    }
    // php bin/phpunit tests/Proxmox/Vm/Application/Service/PatchVmTest.php
}
class FakeOpenSSLService
{
    public function encrypt(string $value): string
    {
        return 'ENC::' . $value;
    }
    public function decrypt(string $value): string
    {
        if (str_starts_with($value, 'ENC::')) {
            return substr($value, 5);
        }
        throw new \RuntimeException('Invalid encrypted string');
    }
}