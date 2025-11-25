<?php
declare(strict_types=1);

namespace Node\Application\Service;

use Faker\Generator;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Node\Application\Service\PatchNodeService;
use GridCP\Node\Domain\Exception\NodeNotExistError;
use GridCP\Node\Domain\Exception\NodoDuplicated;
use Faker\Factory as FakerFactory;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetFloatGroupEntityByUuidQueried;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupEntityResponse;
use GridCP\Node\Domain\VO\Cpu;
use GridCP\Node\Domain\VO\CpuCustom;
use GridCP\Node\Domain\VO\CpuName;
use GridCP\Node\Domain\VO\CpuVendor;
use GridCP\Node\Domain\VO\FloatgroupsUuids;
use GridCP\Node\Domain\VO\FloatgroupUuid;
use GridCP\Node\Domain\VO\NodeDisplay;
use GridCP\Node\Domain\VO\NodeVPEIp;
use GridCP\Node\Domain\VO\NodeKeyboard;
use GridCP\Node\Domain\VO\NodeGCPName;
use GridCP\Node\Domain\VO\NodeNetworkInterface;
use GridCP\Node\Domain\VO\Node;
use GridCP\Node\Domain\VO\Noderiority;
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
use GridCP\Node\Infrastructure\DB\MySQL\Repository\NodeFloatGroupRepository;
use GridCP\Node\Infrastructure\DB\MySQL\Repository\NodeRepository;
use Node\Application\Helpers\NodeTestTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PatchNodesByUuidTest extends TestCase
{

    protected Generator $faker;
    protected NodeRepository $nodeRepository;
    protected NodeFloatGroupRepository $nodeFloatgroupRepository;
    protected QueryBus $queryBusMock;
    private PatchNodeService $patchNode;
    private Node $node;
    private NodeEntity $nodeEntity;
    private string $nodeGCPName;
    private string $nodeVPEName;

    public function setUp(): void
    {
        $this->faker = FakerFactory::create();
    
        $this->nodeGCPName = $this->faker->name();
        $this->nodeVPEName = $this->faker->name();
        $floatgroupUuid = UuidValueObject::random()->value();
        $floatgroupsUuids = new FloatgroupsUuids([
            $floatgroupUuid
        ]);
    
        $this->node = new Node(
            new NodeUuid(UuidValueObject::random()->value()),
            new NodeGCPName($this->nodeGCPName),
            new NodeVPEName($this->nodeVPEName),
            new NodeVPEHostName($this->faker->domainName()),
            new NodeVPEUsername($this->faker->userName()),
            new NodeVPEPassword($this->faker->password()),
            new NodeVPERealm('pam'),
            new NodeVPEPort($this->faker->randomNumber(4)),
            new NodeVPEIp($this->faker->ipv4()),
            new NodeSshPort($this->faker->randomNumber(4)),
            new NodeTimeZone($this->faker->timezone()),
            new NodeKeyboard(substr($this->faker->regexify('[A-Z]{3}'), 0, 2)), // Max 2 caracteres
            new NodeDisplay($this->faker->text(10)),
            new NodeStorage($this->faker->text(10)),
            new NodeStorageIso($this->faker->text(10)),
            new NodeStorageImage($this->faker->text(10)),
            new NodeStorageBackup($this->faker->text(10)),
            new NodeNetworkInterface($this->faker->text(10)),
            new Cpu(
                new CpuName("GenuineIntel"),
                new CpuVendor("Cascadelake-Server-v4"),
                new CpuCustom(0)
            ),
            new Noderiority(1),
            $floatgroupsUuids
        );







        $loggerMock = $this->createMock(LoggerInterface::class);
      //  $this->queryBusMock = $this->createMock(QueryBus::class);

        $floatgroupUuidVo = new FloatgroupUuid($floatgroupUuid);
        $floatgroupEntity = new Ip4FloatGroupEntity();
        $floatgroupEntity->setUuid($floatgroupUuidVo->value());


        $this->queryBusMock = $this->getMockBuilder(QueryBus::class)
                            ->disableOriginalConstructor()
                            ->getMock();
    
        $this->nodeRepository =  $this->getMockBuilder(nodeRepository::class)->disableOriginalConstructor()->getMock();
        $this->nodeFloatgroupRepository =  $this->getMockBuilder(NodeFloatGroupRepository::class)->disableOriginalConstructor()->getMock();
    //    $this->nodeFloatgroupRepository = $this->createMock(NodeFloatGroupRepository::class);
    
        $this->patchNode = new PatchNodeService(
            $this->nodeRepository,
            $this->nodeFloatgroupRepository, 
            $loggerMock, 
            $this->queryBusMock
        );
        $this->queryBusMock
                            ->method('ask')
                            ->willReturn( new FloatGroupEntityResponse($floatgroupEntity) );   


        
        $this->nodeEntity = new NodeEntity();
        $this->nodeEntity->id  = 1;
        $this->nodeEntity->setGcpName($this->nodeGCPName);
        $this->nodeEntity->setPveName($this->nodeVPEName);

    }

    public function testPathNodeSuccess(): void
    {
        $this->nodeFloatgroupRepository
                            ->expects($this->once())
                            ->method('findAllByNodeId')
                            ->with($this->nodeEntity->getId())
                            ->willReturn([] );



        $this->nodeRepository
        ->expects($this->once())
        ->method('findByUuid')
        ->with($this->node->uuid()->value())
        ->willReturn($this->nodeEntity);

        $this->nodeRepository
            ->expects($this->once())
            ->method('findOneByGCPName')
            ->with($this->nodeGCPName)
            ->willReturn(null);

        $this->nodeRepository
            ->expects($this->once())
            ->method('findOneByVPEName')
            ->with($this->nodeVPEName)
            ->willReturn(null);

        $this->nodeEntity->setGcpName($this->nodeGCPName);
        $this->nodeEntity->setPveName($this->nodeVPEName);

        $this->nodeRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->nodeEntity);

        $this->patchNode->__invoke($this->node);
    }

    public function testPatchNodeThrowsNodeNotExistError(): void
    {
        $this->nodeRepository
            ->expects($this->once())
            ->method('findByUuid')
            ->with($this->node->uuid()->value())
            ->willReturn(null);

        $this->expectException(NodeNotExistError::class);

        $this->patchNode->__invoke($this->node);
    }

    public function testPatchNodeThrowsNodoDuplicatedError(): void
    {
        $nodeEntity = new NodeEntity();
        $nodeEntity->setGcpName($this->node->node_gcp_name()->value());

        $existingNode = new NodeEntity();
        $existingNode->setGcpName($this->node->node_gcp_name()->value());

        $this->nodeRepository
            ->expects($this->once())
            ->method('findByUuid')
            ->with($this->node->uuid()->value())
            ->willReturn($nodeEntity);

        $this->nodeRepository
            ->expects($this->once())
            ->method('findOneByGCPName')
            ->with($this->node->node_gcp_name()->value())
            ->willReturn($existingNode);

        $this->expectException(NodoDuplicated::class);
        $this->expectExceptionMessage($this->node->node_gcp_name()->value());

        $this->patchNode->__invoke($this->node);
    }

    //     php bin/phpunit tests/Node/Application/Service/PatchNodesByUuidTest.php


}