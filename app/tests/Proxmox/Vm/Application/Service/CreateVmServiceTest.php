<?php
declare(strict_types=1);

namespace Tests\Proxmox\Vm\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;

use GridCP\Proxmox\Vm\Application\Service\CreateVmService;
use PHPUnit\Framework\TestCase;

use GridCP\Proxmox\Vm\Domain\VO\Vm;

use DateTime;
use Exception;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Client\Application\Cqrs\Queries\GetClientEntityByUuidQuerie;
use GridCP\Client\Application\Response\ClientEntityResponse;
use GridCP\Client\Infrastructure\DB\MySQL\Entity\ClientEntity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetIp4WithRelationsQueried;
use GridCP\Net\Ip4\Application\Response\Ip4sNotExitsResponses;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\PostOwnerSubnetQueried;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\PostSubnetQueried;
use GridCP\Net\Ip4Subnet\Application\Response\CreatedOwnerSubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Response\CreatedSubnetResponse;
use GridCP\Node\Application\Cqrs\Queries\SearchNodeByNameQuerie;
use GridCP\Proxmox\Common\Infrastructure\ProxmoxClientService;
use GridCP\Proxmox\Vm\Application\Helpers\IpsTrait;
use GridCP\Proxmox\Vm\Domain\Repository\IVmIp4Repository;
use GridCP\Proxmox\Vm\Domain\Repository\IVmRepository;

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
use GridCP\Proxmox\Vm\Domain\VO\VmOs;
use GridCP\Proxmox\Vm\Domain\VO\VmPassword;
use GridCP\Proxmox\Vm\Domain\VO\VmStorage;
use GridCP\Proxmox\Vm\Domain\VO\VmTrafficLimit;
use GridCP\Proxmox\Vm\Domain\VO\VmUserName;
use GridCP\Proxmox\Vm\Domain\VO\VmUuid;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmEntity;
use Psr\Log\LoggerInterface;
use Tests\Proxmox\Vm\Application\Service\VmHelper;

use GridCP\Proxmox\Cpu\Application\Cqrs\Queries\AllCpusQueried;

use GridCP\Proxmox\Storage\Application\Cqrs\Queries\GetPromoxStorageQueried;
use GridCP\Proxmox\Storage\Application\Response\StorageResponses;
use GridCP\Proxmox\Storage\Domain\Exception\GetProxmoxStorageServiceException;

use GridCP\Proxmox\Vm\Domain\Exception\IpNotFoundException;
use GridCP\Proxmox\Vm\Domain\Exception\ListNodesEmptyError;
use GridCP\Proxmox\Vm\Domain\Exception\SubnetHasNotCreateException;
use GridCP\Proxmox\Vm\Domain\VO\VmNodePveName;
use Tests\Proxmox\Vm\Application\Service\So\SoXConcrete;
use GridCP\Proxmox_Client\Nodes\Domain\Responses\NodesResponse as NodesResponseApiProxmox;
use GridCP\Proxmox\Vm\Domain\Exception\CreateVmException;
use Tests\Proxmox\Vm\Application\Service\So\SoXConcreteError;

class CreateVmServiceTest extends TestCase
{
    use VmHelper, IpsTrait;
    private IVmRepository $vmRepository;
    private IVmIp4Repository $vmIpRepository;
    public LoggerInterface $logger;
    private QueryBus $queryBus;
    private CreateVmService $createVm4Service;
    private ProxmoxClientService $proxmoxClientService;


    private IpVo $ipVo;
    private VmEntity $vmEntity;
    private Ip4Entity $ipEntity;
    private Vm $VmVo;
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

    private array $ipResponse;


    public function setUp():void
    {
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




        $this->vmRepository = $this->getMockBuilder(IVmRepository::class)->disableOriginalConstructor()->getMock();
        $this->proxmoxClientService = $this->createMock(ProxmoxClientService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBus = $this->createMock(QueryBus::class);

        $this->createVm4Service = new CreateVmService($this->vmRepository, $this->logger, $this->queryBus, $this->proxmoxClientService);


        $this->VmVo = new Vm(
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
                            'uuid' => $this->VmVo->floatgroupUuid()->value(),
                            'name' => 'Ubrique',
                            'active' => 1,
                        ],
        ];
        $network = [
            'id' => 1,
            'uuid' => $this->faker->uuid(),
            'name' => $this->VmVo->netIp()->value(),
            'mask' => $this->VmVo->mask()->value(),
            'gateway' => $this->VmVo->netGw()->value(),
            'active' => 1,
            'floatGroups' => $floatroups,
        ];
        $this->ipResponse = [
            'ip' => [
                    'id' => 1,
                    'uuid' => $this->faker->uuid(),
                    'address' => $this->VmVo->netIp()->value(),
                    'active' => 1,
                    ],
            'network' => $network,
        ];
        $this->ipEntity = $this->ip4Entity($this->VmVo);
    }

    public function testCreateVMReturnsUuid()
    {
        $ipsResponse = new Ip4sNotExitsResponses($this->ipResponse);
        $clientEntity = new ClientEntityResponse(new ClientEntity());
        $floatgroupuuid = $this->getFloatgroupUuid($ipsResponse->gets());
        $subnetEntity = $this->subnetEntity($this->VmVo, $floatgroupuuid);
        $subnetResponse = new CreatedSubnetResponse($subnetEntity->getUuid());
        $ownerResponse = new CreatedOwnerSubnetResponse($this->faker->uuid());
        $nodeResponse = $this->nodeResponse($this->VmVo, $this->vmPVENode->value());
        $nodeProxmox = $this->nodesResponseApiPROXMOX($this->vmPVENode->value());
        $nodeClientProxmox = [$nodeProxmox, $nodeResponse];
        $storageResponse = new StorageResponses([]);
    
        $this->proxmoxClientService->expects($this->once())
            ->method('getNodesWithAuth')
            ->with($this->VmVo->gcpNode()->value())
            ->willReturn($nodeClientProxmox);

    
        $this->VmVo->setPveNode($nodeClientProxmox[1]->pve_name());
    
        $vmId = 8000;
        $this->vmRepository->expects($this->once())
            ->method('findMaxId')
            ->with($this->VmVo->uuid()->value())
            ->willReturn($vmId);
        
        $vmId++;
        $this->VmVo->setVmId(new VmId($vmId));
    
        $so = new SoXConcrete($this->VmVo->os()->value());
        $so->init($this->VmVo->toArray(), $this->proxmoxClientService,$this->logger);
        $yaml = $so->getParameters();
        $cpuResponse = $this->cpuResponse($yaml['vm_cpu_Type']);

        $result = $so->createVM( );
        $this->assertEquals($result['error'], null);
    
        $this->queryBus
                        ->method('ask')
                        ->willReturnCallback(function ($query) use (
                            $ipsResponse,
                            $clientEntity,
                            $subnetResponse,
                            $ownerResponse,
                            $storageResponse,
                            $nodeResponse,
                            $cpuResponse
                        ) {
                            if ($query instanceof GetIp4WithRelationsQueried) {
                                return $ipsResponse;
                            }
                            if ($query instanceof GetClientEntityByUuidQuerie) {
                                return $clientEntity;
                            }
                            if ($query instanceof PostSubnetQueried) {
                                return $subnetResponse;
                            }
                            if ($query instanceof PostOwnerSubnetQueried) {
                                return $ownerResponse;
                            }
                            if ($query instanceof GetPromoxStorageQueried) {
                                return $storageResponse;
                            }
                            if ($query instanceof SearchNodeByNameQuerie) {
                                return $nodeResponse;
                            }
                            if ($query instanceof AllCpusQueried) {
                                return $cpuResponse;
                            }
                            $this->throwClassException($query);
                        });


        $vmEntity = $this->vmEntity($this->VmVo, $nodeClientProxmox[1]->id());
        $this->vmRepository->expects($this->once())
        ->method('save')
        ->with($this->isInstanceOf(VmEntity::class));



        $result = $this->createVm4Service->createVM($this->VmVo, $so);

        $this->assertIsString($result);
        $this->assertEquals($result, $this->VmVo->uuid()->value());
    }


    public function testCreateVmWhenIp4NotExists()
    {
        $ipsResponse = new Ip4sNotExitsResponses([]); // Respuesta vacía

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->with(new GetIp4WithRelationsQueried($this->VmVo->netIp()->value()))
            ->willReturn($ipsResponse);

        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains("ip not found"));

        $this->expectException(IpNotFoundException::class);

        $this->createVm4Service->__invoke($this->VmVo);
    }


    public function testCreateVmWhenCreateSubnetException()
    {

        $ipsResponse = new Ip4sNotExitsResponses($this->ipResponse);
        $clientEntity = new ClientEntityResponse(new ClientEntity());
        $subnetResponse = new CreatedSubnetResponse(new Exception());

        $this->queryBus
            ->method('ask')
            ->willReturnCallback(function ($query) use ($ipsResponse, $clientEntity, $subnetResponse) {
                if ($query instanceof GetIp4WithRelationsQueried) {
                    return $ipsResponse;
                }
                if ($query instanceof GetClientEntityByUuidQuerie) {
                    return $clientEntity;
                }
                if ($query instanceof PostSubnetQueried) {
                    return $subnetResponse;
                }
                $this->throwClassException($query);
            });
    

            $this->expectException(SubnetHasNotCreateException::class);
        $this->expectExceptionMessage('Error. Subnet has not been created');
    

        $this->createVm4Service->createVM($this->VmVo);
    }

    public function testCreateVmWhenCreateOwnerSubnetException()
    {

        $ipsResponse = new Ip4sNotExitsResponses($this->ipResponse);
        $clientEntity = new ClientEntityResponse(new ClientEntity());
        $floatgroupuuid = $this->getFloatgroupUuid($ipsResponse->gets());
        $subnetEntity = $this->subnetEntity($this->VmVo, $floatgroupuuid);
        $subnetResponse = new CreatedSubnetResponse($subnetEntity->getUuid());
        $ownerResponse = new CreatedOwnerSubnetResponse(new Exception()); 
    

        $this->queryBus
            ->method('ask')
            ->willReturnCallback(function ($query) use ($ipsResponse, $clientEntity, $subnetResponse, $ownerResponse) {
                if ($query instanceof GetIp4WithRelationsQueried) {
                    return $ipsResponse;
                }
                if ($query instanceof GetClientEntityByUuidQuerie) {
                    return $clientEntity;
                }
                if ($query instanceof PostSubnetQueried) {
                    return $subnetResponse;
                }
                if ($query instanceof PostOwnerSubnetQueried) {
                    return $ownerResponse;
                }
                $this->throwClassException($query);
            });

        $this->expectException(SubnetHasNotCreateException::class);
        $this->expectExceptionMessage('Error. Subnet has not been created');
    

        $this->createVm4Service->createVM($this->VmVo);
    }

    public function testCreateVmWhenNodeNotFound()
    {

        $ipsResponse = new Ip4sNotExitsResponses($this->ipResponse);
        $clientEntity = new ClientEntityResponse(new ClientEntity());
        $floatgroupuuid = $this->getFloatgroupUuid($ipsResponse->gets());
        $subnetEntity = $this->subnetEntity($this->VmVo, $floatgroupuuid);
        $subnetResponse = new CreatedSubnetResponse($subnetEntity->getUuid());
        $ownerResponse = new CreatedOwnerSubnetResponse($this->faker->uuid());
    
        $this->proxmoxClientService->expects($this->once())
            ->method('getNodesWithAuth')
            ->with($this->VmVo->gcpNode()->value())
            ->willReturn(null);
        
        
        $this->queryBus
                        ->method('ask')
                        ->willReturnCallback(function ($query) use (
                            $ipsResponse,
                            $clientEntity,
                            $subnetResponse,
                            $ownerResponse
                        ) {
                            if ($query instanceof GetIp4WithRelationsQueried) {
                                return $ipsResponse;
                            }
                            if ($query instanceof GetClientEntityByUuidQuerie) {
                                return $clientEntity;
                            }
                            if ($query instanceof PostSubnetQueried) {
                                return $subnetResponse;
                            }
                            if ($query instanceof PostOwnerSubnetQueried) {
                                return $ownerResponse;
                            }
                            $this->throwClassException($query);
                        });

        $this->expectException(ListNodesEmptyError::class);
    

        $this->createVm4Service->createVM($this->VmVo);
    }

    public function testCreateVmWhenNodeNotExistInProxmox()
    {

        $ipsResponse = new Ip4sNotExitsResponses($this->ipResponse);
        $clientEntity = new ClientEntityResponse(new ClientEntity());
        $floatgroupuuid = $this->getFloatgroupUuid($ipsResponse->gets());
        $subnetEntity = $this->subnetEntity($this->VmVo, $floatgroupuuid);
        $subnetResponse = new CreatedSubnetResponse($subnetEntity->getUuid());
        $ownerResponse = new CreatedOwnerSubnetResponse($this->faker->uuid());
        $nodeResponse = $this->nodeResponse($this->VmVo, $this->vmPVENode->value());
        $nodeProxmox = new NodesResponseApiProxmox (...[]);
        $nodeClientProxmox = [$nodeProxmox, $nodeResponse];
    
        $this->proxmoxClientService->expects($this->once())
            ->method('getNodesWithAuth')
            ->with($this->VmVo->gcpNode()->value())
            ->willReturn($nodeClientProxmox);
        
        
        $this->queryBus
                        ->method('ask')
                        ->willReturnCallback(function ($query) use (
                            $ipsResponse,
                            $clientEntity,
                            $subnetResponse,
                            $ownerResponse,
                            $nodeResponse
                        ) {
                            if ($query instanceof GetIp4WithRelationsQueried) {
                                return $ipsResponse;
                            }
                            if ($query instanceof GetClientEntityByUuidQuerie) {
                                return $clientEntity;
                            }
                            if ($query instanceof PostSubnetQueried) {
                                return $subnetResponse;
                            }
                            if ($query instanceof PostOwnerSubnetQueried) {
                                return $ownerResponse;
                            }
                            if ($query instanceof SearchNodeByNameQuerie) {
                                return $nodeResponse;
                            }
                            $this->throwClassException($query);
                        });

        $this->expectException(ListNodesEmptyError::class);


        $this->createVm4Service->createVM($this->VmVo);
    }

    public function testCreateVmWhenSotorageException()
    {

        $ipsResponse = new Ip4sNotExitsResponses($this->ipResponse);
        $clientEntity = new ClientEntityResponse(new ClientEntity());
        $floatgroupuuid = $this->getFloatgroupUuid($ipsResponse->gets());
        $subnetEntity = $this->subnetEntity($this->VmVo, $floatgroupuuid);
        $subnetResponse = new CreatedSubnetResponse($subnetEntity->getUuid());
        $ownerResponse = new CreatedOwnerSubnetResponse($this->faker->uuid());
        $nodeResponse = $this->nodeResponse($this->VmVo, $this->vmPVENode->value());
        $nodeProxmox = $this->nodesResponseApiPROXMOX($this->vmPVENode->value());
        $nodeClientProxmox = [$nodeProxmox, $nodeResponse];
        $storageResponse = new GetProxmoxStorageServiceException(new Exception());
    
        $this->proxmoxClientService->expects($this->once())
            ->method('getNodesWithAuth')
            ->with($this->VmVo->gcpNode()->value())
            ->willReturn($nodeClientProxmox);

    
        $this->VmVo->setPveNode($nodeClientProxmox[1]->pve_name());
        
        
        $this->queryBus
                        ->method('ask')
                        ->willReturnCallback(function ($query) use (
                            $ipsResponse,
                            $clientEntity,
                            $subnetResponse,
                            $ownerResponse,
                            $storageResponse,
                            $nodeResponse
                        ) {
                            if ($query instanceof GetIp4WithRelationsQueried) {
                                return $ipsResponse;
                            }
                            if ($query instanceof GetClientEntityByUuidQuerie) {
                                return $clientEntity;
                            }
                            if ($query instanceof PostSubnetQueried) {
                                return $subnetResponse;
                            }
                            if ($query instanceof PostOwnerSubnetQueried) {
                                return $ownerResponse;
                            }
                            if ($query instanceof GetPromoxStorageQueried) {
                                throw $storageResponse;
                            }
                            if ($query instanceof SearchNodeByNameQuerie) {
                                return $nodeResponse;
                            }
                            $this->throwClassException($query);
                        });

        $this->expectException(GetProxmoxStorageServiceException::class);
    

        $this->createVm4Service->createVM($this->VmVo);
    }

    public function testCreateVMInCreateProxmoxError()
    {
        $ipsResponse = new Ip4sNotExitsResponses($this->ipResponse);
        $clientEntity = new ClientEntityResponse(new ClientEntity());
        $floatgroupuuid = $this->getFloatgroupUuid($ipsResponse->gets());
        $subnetEntity = $this->subnetEntity($this->VmVo, $floatgroupuuid);
        $subnetResponse = new CreatedSubnetResponse($subnetEntity->getUuid());
        $ownerResponse = new CreatedOwnerSubnetResponse($this->faker->uuid());
        $nodeResponse = $this->nodeResponse($this->VmVo, $this->vmPVENode->value());
        $nodeProxmox = $this->nodesResponseApiPROXMOX($this->vmPVENode->value());
        $nodeClientProxmox = [$nodeProxmox, $nodeResponse];
        $storageResponse = new StorageResponses([]);
    
        $this->proxmoxClientService->expects($this->once())
            ->method('getNodesWithAuth')
            ->with($this->VmVo->gcpNode()->value())
            ->willReturn($nodeClientProxmox);

    
        $this->VmVo->setPveNode($nodeClientProxmox[1]->pve_name());
    
        $vmId = 8000;
        $this->vmRepository->expects($this->once())
            ->method('findMaxId')
            ->with($this->VmVo->uuid()->value())
            ->willReturn($vmId);
        
        $vmId++;
        $this->VmVo->setVmId(new VmId($vmId));
    
        $so = new SoXConcreteError($this->VmVo->os()->value());
        $so->init($this->VmVo->toArray(), $this->proxmoxClientService);
        $yaml = $so->getParameters();
        $cpuResponse = $this->cpuResponse($yaml['vm_cpu_Type']);


        $result = $so->createVM( );


        $this->queryBus
                        ->method('ask')
                        ->willReturnCallback(function ($query) use (
                            $ipsResponse,
                            $clientEntity,
                            $subnetResponse,
                            $ownerResponse,
                            $storageResponse,
                            $nodeResponse,
                            $cpuResponse
                        ) {
                            if ($query instanceof GetIp4WithRelationsQueried) {
                                return $ipsResponse;
                            }
                            if ($query instanceof GetClientEntityByUuidQuerie) {
                                return $clientEntity;
                            }
                            if ($query instanceof PostSubnetQueried) {
                                return $subnetResponse;
                            }
                            if ($query instanceof PostOwnerSubnetQueried) {
                                return $ownerResponse;
                            }
                            if ($query instanceof GetPromoxStorageQueried) {
                                return $storageResponse;
                            }
                            if ($query instanceof SearchNodeByNameQuerie) {
                                return $nodeResponse;
                            }
                            if ($query instanceof AllCpusQueried) {
                                return $cpuResponse;
                            }
                            $this->throwClassException($query);
                        });


                        $this->expectException(CreateVmException::class);
                        $this->expectExceptionMessage('Error create Vm in PROXMOX. Error -> ');



        $result = $this->createVm4Service->createVM($this->VmVo, $so);


    }

    private function throwClassException ($query) {
        throw new \Exception("Unexpected query: " . get_class($query));
    }



    
    /*
    php bin/phpunit tests/Proxmox/Vm/Application/Service/CreateVmServiceTest.php
    php bin/phpunit tests/Proxmox/Vm/Presentation/Rest/V1/E2ECreateVmTest.php
 
    */
}