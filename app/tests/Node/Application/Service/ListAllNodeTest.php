<?php
declare(strict_types=1);

namespace Node\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Node\Application\Response\NodeResponse;
use GridCP\Node\Application\Service\ListNodeByUuidservice;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeEntity;
use GridCP\Node\Infrastructure\DB\MySQL\Repository\NodeRepository;
use PHPUnit\Framework\TestCase;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Node\Application\Response\CpuResponse;
use GridCP\Node\Application\Response\NodeResponses;
use GridCP\Node\Application\Service\ListAllNodesService;
use GridCP\Node\Domain\Exception\ListNodesEmptyException;
use GridCP\Node\Domain\VO\Cpu;
use GridCP\Node\Domain\VO\CpuCustom;
use GridCP\Node\Domain\VO\CpuName;
use GridCP\Node\Domain\VO\CpuVendor;
use GridCP\Node\Domain\VO\Node;
use GridCP\Node\Domain\VO\NodeDisplay;
use GridCP\Node\Domain\VO\NodeVPEIp;
use GridCP\Node\Domain\VO\NodeKeyboard;
use GridCP\Node\Domain\VO\NodeGCPName;
use GridCP\Node\Domain\VO\NodeNetworkInterface;
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

class ListAllNodeTest extends TestCase
{
    protected Generator $faker;
    protected NodeRepository $nodeRepository;

    protected ListAllNodesService $listNode;
    private NodeResponses $nodes;
    private array $nodesEntity = [];

    public function setUp(): void
    {
        $this->nodeRepository = $this->getMockBuilder(NodeRepository::class)->disableOriginalConstructor()->getMock();
        $this->listNode = new ListAllNodesService($this->nodeRepository);

        $node1 = $this->createNode();
        $node2 = $this->createNode();
        $nodeReponse1 = $this->createNodeResponse($node1);
        $nodeReponse2 = $this->createNodeResponse($node2);
        $this->nodes = new NodeResponses( $nodeReponse1,$nodeReponse2);

        $this->nodesEntity[] = $this->createEntityNode($node1);
        $this->nodesEntity[] = $this->createEntityNode($node2);
    }

    private function createNodeResponse(Node $node):NodeResponse
    {
       

        return   new NodeResponse(
            $node->uuid()->value(),
            $node->node_gcp_name()->value(),
            $node->node_vpe_name()->value(),
            $node->vpe_hostName()->value(),
            $node->vpe_username()->value(),
            $node->vpe_password()->value(),
            $node->vpe_realm()->value(),
            $node->vpe_port()->value(),
            $node->vpe_ip()->value(),
            $node->sshPort()->value(),
            $node->timeZone()->value(),
            $node->keyboard()->value(),
            $node->display()->value(),
            $node->storage()->value(),
            $node->storageIso()->value(),
            $node->storageImage()->value(),
            $node->storageBackup()->value(),
            $node->networkInterface()->value(),
            new CpuResponse(
                $node->cpu()->vendor()->value(),
                $node->cpu()->name()->value(),
                $node->cpu()->custom()->value()
            )
        );
    }


    private function createNode():Node
    {
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
        $nodeKeyboard = new NodeKeyboard($this->faker->randomLetter());
        $nodeDisplay = new NodeDisplay($this->faker->text(20));
        $nodeStorage = new NodeStorage($this->faker->text(20));
        $nodeStorageIso = new NodeStorageIso($this->faker->text(20));
        $nodeStorageImage = new NodeStorageImage($this->faker->text(20));
        $nodeStorageBackup = new NodeStorageBackUp($this->faker->text(20));
        $nodeNetworkInterface = new NodeNetworkInterface($this->faker->text(20));

        $cpuName = new CpuName("GenuineIntel");
        $cpuVendor = new CpuVendor("Cascadelake-Server-v4");
        $cpuCustom = new CpuCustom(0);
        $cpu = new Cpu($cpuName, $cpuVendor, $cpuCustom);
        
        return new Node(
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
                                    $cpu
                                );
    }
    
    private function createEntityNode(Node $node):NodeEntity
    {
        $nodeEntity = new NodeEntity();
        $nodeEntity->setUuid($node->uuid()->value());
        $nodeEntity->setGcpName($node->node_gcp_name()->value());
        $nodeEntity->setPveName($node->node_vpe_name()->value());
        $nodeEntity->setPveHostName($node->vpe_hostName()->value());
        $nodeEntity->setPveUserName($node->vpe_username()->value());
        $nodeEntity->setPvePassword($node->vpe_password()->value());
        $nodeEntity->setPveRealm($node->vpe_realm()->value());
        $nodeEntity->setPvePort($node->vpe_port()->value());
        $nodeEntity->setPveIp($node->vpe_ip()->value());
        $nodeEntity->setSshPort($node->sshPort()->value());
        $nodeEntity->setTimezone($node->timeZone()->value());
        $nodeEntity->setKeyboard($node->keyboard()->value());
        $nodeEntity->setDisplay($node->display()->value());
        $nodeEntity->setStorage($node->storage()->value());
        $nodeEntity->setStorageIso($node->storageIso()->value());
        $nodeEntity->setStorageImage($node->storageImage()->value());
        $nodeEntity->setStorageBackup($node->storageBackup()->value());
        $nodeEntity->setNetworkInterface($node->networkInterface()->value());
        $nodeEntity->setCpu($node->cpu()->value());

        return $nodeEntity;
    }

    public function testNodeSearchByUUIDOK():void
    {
        
        $this->nodeRepository->expects($this->any())
            ->method('getAll')
            ->willReturn($this->nodesEntity);

        $result = $this->listNode->__invoke();
        $this->assertInstanceOf(NodeResponses::class , $result);
        $this->assertIsArray($result->nodes());
    }
    
    public function testNodeSearchByUUIDNotExist():void
    {
        $this->nodeRepository->expects($this->any())
            ->method('getAll')
            ->willReturn([]);

        $this->expectException(ListNodesEmptyException::class);
        $this->listNode->__invoke();
    }
}