<?php
declare(strict_types=1);

namespace Proxmox\Vm\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use PHPUnit\Framework\TestCase;
use GridCP\Proxmox\Vm\Application\Helpers\ProxmoxVmFunctions;
use GridCP\Proxmox\Vm\Application\Response\CpuEntityResponse;
use GridCP\Proxmox\Vm\Application\Response\NodeEntityResponse;
use GridCP\Proxmox\Vm\Application\Response\VmEntityResponse;
use GridCP\Proxmox\Vm\Application\Service\GetVmByUuidservice;
use GridCP\Proxmox\Vm\Domain\Exception\VmNotFound;
use GridCP\Proxmox\Vm\Domain\Repository\IVmRepository;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\NodeEntity;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmEntity;

class ListVmByUuidTest extends TestCase
{
    protected Generator $faker;
    protected IVmRepository $vmRepository;
    protected QueryBus $queryBusMock;

    protected GetVmByUuidservice $listVm;
    use ProxmoxVmFunctions;

    public function setUp(): void
    {
        $this->vmRepository = $this->getMockBuilder(IVmRepository::class)->disableOriginalConstructor()->getMock();
        $this->queryBusMock = $this->createMock(QueryBus::class);
       // $this->listVm = new GetVmByUuidservice($this->vmRepository,$queryBusMock);
        $this->listVm = $this->createMock(GetVmByUuidService::class);
        $this->faker = FakerFactory::create();
    }

    public function testVmByUUIDOK():void
    {
        $nodeId = 1;
        $vmEntity = new VmEntity();
        $vmEntity->setId(1);
        $vmEntity->setUuid($this->faker->uuid());
        $vmEntity->setName($this->faker->name());
        $vmEntity->setCores($this->faker->randomNumber(2));
        $vmEntity->setCpu($this->faker->name());
        $vmEntity->setOs($this->faker->name());
        $vmEntity->setActive(true); 
        $vmEntity->setIdNode($nodeId);




        $id = $nodeId;
        $uuid = $this->faker->uuid();
        $gcpName = $this->faker->name();
        $pveName = $this->faker->name();
        $pveHostName = $this->faker->name();
        $pveUserName = $this->faker->userName();
        $pvePassword = $this->faker->password();
        $pveRealm = $this->faker->name();
        $pvePort = $this->faker->randomNumber(4);
        $pveIp = $this->faker->ipv4();
        $sshPort = $this->faker->randomNumber(4);
        $timezone = $this->faker->timezone();
        $keyboard = $this->faker->randomLetter();
        $display = $this->faker->text(20);
        $storage = $this->faker->text(20);
        $storageIso = $this->faker->text(20);
        $storageImage = $this->faker->text(20);
        $storageBackup = $this->faker->text(20);
        $networkInterface = $this->faker->text(20);
        $cpu = new CpuEntityResponse(
            "GenuineIntel",
            "Cascadelake-Server-v48",
            0
        );
        $nodeEntity = new NodeEntityResponse(
            $id,
            $uuid,
            $gcpName,
            $pveName,
            $pveHostName,
            $pveUserName,
            $pvePassword,
            $pveRealm,
            $pvePort,
            $pveIp,
            $sshPort,
            $timezone,
            $keyboard,
            $display,
            $storage,
            $storageIso,
            $storageImage,
            $storageBackup,
            $networkInterface,
            $cpu,
            );
        $result = $this->vmToResponse($vmEntity, $nodeEntity);



        $this->vmRepository->expects($this->any())
            ->method('findByUuid')
            ->with($vmEntity->uuid())
            ->willReturn($vmEntity);

        // $result = $this->listVm->__invoke($vmEntity->uuid());
        $this->listVm->method('__invoke')->willReturn($result);




       

        $this->assertInstanceOf(VmEntityResponse::class , $result);

        $this->assertEquals($vmEntity->name(), $result->name());
        //

        $this->assertInstanceOf(NodeEntityResponse::class , $result->node());
        $this->assertInstanceOf(CpuEntityResponse::class , $result->node()->cpu());

        $this->assertIsArray($result->node()->toArray());
        $this->assertIsArray($result->node()->cpu()->toArray());
    }

    public function testVmByUUIDNotExist():void
    {
        $uuid = $this->faker->uuid();
        $this->vmRepository->expects($this->any())
                           ->method('findByUuid')
                           ->with($uuid)
                           ->willReturn(null);

        $this->expectException(VmNotFound::class);
        $vm = new GetVmByUuidservice($this->vmRepository,$this->queryBusMock);
        $vm->__invoke($uuid);
    }
    // php bin/phpunit tests/Proxmox/Vm/Application/Service/ListVmByUuidTest.php
}