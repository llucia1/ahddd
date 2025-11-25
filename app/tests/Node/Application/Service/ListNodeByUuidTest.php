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
use GridCP\Node\Domain\Exception\ListNodesEmptyException;
use GridCP\Node\Domain\VO\Cpu;
use GridCP\Node\Domain\VO\CpuCustom;
use GridCP\Node\Domain\VO\CpuName;
use GridCP\Node\Domain\VO\CpuVendor;
use GridCP\Node\Domain\VO\FloatgroupsUuids;
use GridCP\Node\Domain\VO\Node;
use GridCP\Node\Domain\VO\NodeDisplay;
use GridCP\Node\Domain\VO\NodeVPEIp;
use GridCP\Node\Domain\VO\NodeKeyboard;
use GridCP\Node\Domain\VO\NodeGCPName;
use GridCP\Node\Domain\VO\NodeNetworkInterface;
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

class ListNodeByUuidTest extends TestCase
{
    protected Generator $faker;
    protected NodeRepository $nodeRepository;

    protected ListNodeByUuidservice $listNode;
    private Node $node;

    public function setUp(): void
    {
        $this->nodeRepository = $this->getMockBuilder(NodeRepository::class)->disableOriginalConstructor()->getMock();
        $this->listNode = new ListNodeByUuidservice($this->nodeRepository);
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
        $priority = new Noderiority(1);
        $f1 = UuidValueObject::random()->value();
        $f2 = UuidValueObject::random()->value();
        $floatgroupsUuid = new FloatgroupsUuids([$f1, $f2]);
        
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
                                    $floatgroupsUuid

                                );
    }

    public function testNodeSearchByUUIDOK():void
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
        $nodeEntity->setCpu($this->node->cpu()->value());
        $this->nodeRepository->expects($this->any())
            ->method('findByUuid')
            ->with($this->node->uuid()->value())
            ->willReturn($nodeEntity);

        $result = $this->listNode->__invoke($this->node->uuid()->value());
        $this->assertInstanceOf(NodeResponse::class , $result);
        $this->assertEquals($this->node->uuid()->value(), $result->uuid());
        $this->assertEquals($this->node->node_gcp_name()->value(), $result->gcp_name());
        $this->assertIsArray($result->cpu());
        $this->assertEquals($this->node->cpu()->value(), $result->cpu());
    }

    public function testNodeSearchByUUIDNotExist():void
    {
        $this->nodeRepository->expects($this->any())
            ->method('findByUuid')
            ->with($this->node->uuid()->value())
            ->willReturn(null);

        $this->expectException(ListNodesEmptyException::class);
        $this->listNode->__invoke($this->node->uuid()->value());
    }
}