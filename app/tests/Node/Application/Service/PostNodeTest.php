<?php
declare(strict_types=1);

namespace Node\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\Bus\EventSource\EventBus;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetAllNodeByFloatgroupUuidQueried;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupEntityResponse;
use GridCP\Node\Application\Service\CreateNodeService;

use GridCP\Node\Domain\Exception\NodoDuplicatedException;
use GridCP\Node\Domain\Repository\INodeFloatgroupRepository;
use GridCP\Node\Domain\VO\Cpu;
use GridCP\Node\Domain\VO\CpuCustom;
use GridCP\Node\Domain\VO\CpuName;
use GridCP\Node\Domain\VO\CpuVendor;
use GridCP\Node\Domain\VO\FloatgroupsUuids;
use GridCP\Node\Domain\VO\Node;
use GridCP\Node\Domain\VO\NodeDisplay;
use GridCP\Node\Domain\VO\Noderiority;
use GridCP\Node\Domain\VO\NodeVPEIp;
use GridCP\Node\Domain\VO\NodeKeyboard;
use GridCP\Node\Domain\VO\NodeGCPName;
use GridCP\Node\Domain\VO\NodeNetworkInterface;
use GridCP\Node\Domain\VO\NodeOsName;
use GridCP\Node\Domain\VO\NodeVPEHostName;
use GridCP\Node\Domain\VO\NodeVPEPassword;
use GridCP\Node\Domain\VO\NodeVPEPort;
use GridCP\Node\Domain\VO\NodeVPERealm;
use GridCP\Node\Domain\VO\NodeVPEUsername;
use GridCP\Node\Domain\VO\NodeSshPort;
use GridCP\Node\Domain\VO\NodeStorage;
use GridCP\Node\Domain\VO\NodeStorageBackUp;
use GridCP\Node\Domain\VO\NodeStorageImage;
use GridCP\Node\Domain\VO\NodeStorageIso;
use GridCP\Node\Domain\VO\NodeTimeZone;
use GridCP\Node\Domain\VO\NodeUuid;
use GridCP\Node\Domain\VO\NodeVPEName;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeEntity;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeFloatGroupEntity;
use GridCP\Node\Infrastructure\DB\MySQL\Repository\NodeRepository;
use GridCP\Proxmox\Os\Application\Cqrs\Queries\getOsEntityByNameQueried;
use GridCP\Proxmox\Os\Application\Response\OsResponsesQuery;
use GridCP\Proxmox\Os\Infrastructure\DB\MySQL\Entity\OsEntity;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class PostNodeTest extends TestCase
{
    protected Generator $faker;

    protected NodeRepository $nodeRepository;
    protected INodeFloatgroupRepository $nodeFloatgroupRepository;
    private MockObject $logger;
    private MockObject $queryBus;
    private MockObject $bus;

    private CreateNodeService $postNode;

    private Node $node;
    private FloatgroupsUuids $floatgroupsUuids;

    public function setUp(): void
    {
        $EventBusMock = $this->createMock(EventBus::class);

        $this->nodeRepository = $this->getMockBuilder(nodeRepository::class)->disableOriginalConstructor()->getMock();
        $this->nodeFloatgroupRepository = $this->getMockBuilder(INodeFloatgroupRepository::class)->disableOriginalConstructor()->getMock();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBus = $this->createMock(QueryBus::class);

        $this->postNode = new CreateNodeService(
            $this->nodeRepository, 
            $this->nodeFloatgroupRepository, 
        $this->queryBus,
        $this->logger,
        $EventBusMock);
        $this->faker = FakerFactory::create();

        $nodeUuid = new NodeUuid(UuidValueObject::random()->value());
        $nodeName = new NodeGCPName($this->faker->name());
        $nodeNameVPE = new NodeVPEName($this->faker->name());
        $nodeIp = new NodeVPEIp($this->faker->ipv4());

        $proxmoxHostname = new NodeVPEHostName($this->faker->name());
        $proxmoxUsername = new NodeVPEUsername($this->faker->userName());
        $proxmoxPassword = new NodeVPEPassword($this->faker->password());
        $proxmoxRealm = new NodeVPERealm('pam');
        $proxmoxPort = new NodeVPEPort($this->faker->randomNumber(4));

        $nodeSshPort = new NodeSshPort($this->faker->randomNumber(4));
        $nodeTimeZone = new NodeTimeZone($this->faker->timezone());
        $nodeKeyboard = new NodeKeyboard('es');
        $nodeDisplay = new NodeDisplay('es');
        $nodeStorage = new NodeStorage($this->faker->text(20));
        $nodeStorageIso = new NodeStorageIso($this->faker->text(20));
        $nodeStorageImage = new NodeStorageImage($this->faker->text(20));
        $nodeStorageBackup = new NodeStorageBackUp($this->faker->text(20));
        $nodeNetworkInterface = new NodeNetworkInterface($this->faker->text(20));

        $cpuName = new CpuName("GenuineIntel");
        $cpuVendor = new CpuVendor("Cascadelake-Server-v4");
        $cpuCustom = new CpuCustom(0);
        $cpu = new Cpu($cpuName, $cpuVendor, $cpuCustom);


        $priority = new Noderiority(8);
        $this->floatgroupsUuids = new FloatgroupsUuids([$this->faker->uuid()]);
        $osName = new NodeOsName('Debian12');
        
        $this->node = new Node(
                                    $nodeUuid,
                                    $nodeName,
                                    $nodeNameVPE,
                                    $proxmoxHostname,
                                    $proxmoxUsername,
                                    $proxmoxPassword,
                                    $proxmoxRealm,
                                    $proxmoxPort,
                                    $nodeIp,
                                    $nodeSshPort,
                                    $nodeTimeZone,
                                    $nodeKeyboard,
                                    $nodeDisplay,
                                    $nodeStorage,
                                    $nodeStorageIso,
                                    $nodeStorageImage,
                                    $nodeStorageBackup,
                                    $nodeNetworkInterface,
                                    $cpu,
                                    $priority,
                                    $this->floatgroupsUuids,
                                    $osName
                                );
    }

    private function nodeEntity(): NodeEntity
    {
        $nodeEntity = new NodeEntity();
        $nodeEntity->setUuid($this->node->uuid()->value());
        $nodeEntity->setGcpName($this->node->node_gcp_name()->value());
        $nodeEntity->setPveName($this->node->node_vpe_name()->value());
        $nodeEntity->setPveHostName($this->node->vpe_hostName()->value());
        $nodeEntity->setPveUserName($this->node->vpe_username()->value());
        $nodeEntity->setPvePassword($this->node->vpe_password()->value());
        $nodeEntity->setPveRealm($this->node->vpe_realm()->value());
        $nodeEntity->setPvePort($this->node->vpe_port()->value());
        $nodeEntity->setPveIp($this->node->vpe_ip()->value());
        $nodeEntity->setSshPort($this->node->sshPort()->value());
        $nodeEntity->setTimezone($this->node->timeZone()->value());
        $nodeEntity->setKeyboard($this->node->keyboard()->value());
        $nodeEntity->setDisplay($this->node->display()->value());
        $nodeEntity->setStorage($this->node->storage()->value());
        $nodeEntity->setStorageIso($this->node->storageIso()->value());
        $nodeEntity->setStorageImage($this->node->storageImage()->value());
        $nodeEntity->setStorageBackup($this->node->storageBackup()->value());
        $nodeEntity->setNetworkInterface($this->node->networkInterface()->value());

        return $nodeEntity;
    }
    public function testPostNodeSuccess(): void
    {
        $nodeEntity = $this->nodeEntity();

        $this->nodeRepository->expects($this->once())
            ->method('findOneByGCPName')
            ->willReturn(null);




        $fgUuid = $this->floatgroupsUuids->get();
        $fgEntity = new Ip4FloatGroupEntity();
        $fgEntity->setUuid($fgUuid[0]->value());
        $fgEntity->setName($this->faker->name());
        $fgResponse = new FloatGroupEntityResponse( $fgEntity );
        


        $osEntity = new OsEntity();
        $osEntity->setName($this->node->osName()->value());     
        $soResponse = new OsResponsesQuery( $osEntity );
        

        $this->queryBus->expects($this->exactly(2))
            ->method('ask')
            ->willReturnCallback(function ($query) use ($fgUuid, $fgResponse, $soResponse) {
                if ($query instanceof GetAllNodeByFloatgroupUuidQueried && $query->uuid() === $fgUuid[0]->value()) {
                    return $fgResponse;
                }
            
                if ($query instanceof getOsEntityByNameQueried && $query->name() === $this->node->osName()->value()) {
                    return $soResponse;
                }
            
                throw new \RuntimeException('Unexpected query in test: ' . get_class($query));
            });

        $nodeEntity->setOs($soResponse->get());  



            $nodeFloatgroup = new NodeFloatGroupEntity();
            $nodeFloatgroup->setFloatgroup( $fgResponse->get() );
            $nodeFloatgroup->setNode($nodeEntity);

        $this->nodeFloatgroupRepository->expects($this->exactly(0))
                ->method('save')
                ->with($nodeFloatgroup);




        $this->nodeRepository->expects($this->once())
                ->method('save')
                ->with($this->callback(function(NodeEntity $actual) use ($nodeEntity) {
                                        return $actual->getUuid() === $nodeEntity->getUuid()
                                            && $actual->getGcpName() === $nodeEntity->getGcpName();
                                    }));

                
        $resultUuid = $this->postNode->__invoke($this->node);

        $this->assertEquals($this->node->uuid()->value(), $resultUuid);
    }

    public function testPostNodeDuplicate(): void
    {
        $nodeEntity = $this->nodeEntity();


        $this->nodeRepository->expects($this->once())
            ->method('findOneByGCPName')
            ->willReturn($nodeEntity);
            
            try {
                $this->postNode->__invoke($this->node);
            } catch (NodoDuplicatedException $e) {
                $this->assertInstanceOf(NodoDuplicatedException::class, $e);
                return;
            }
        
            $this->fail('Se esperaba una excepción NodoDuplicated');
            
            $this->expectExceptionCode(409);
    }
    
}
    //     php bin/phpunit tests/Node/Application/Service/PostNodeTest.php