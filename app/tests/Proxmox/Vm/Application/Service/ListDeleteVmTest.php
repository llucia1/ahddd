<?php
declare(strict_types=1);

namespace Proxmox\Vm\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use GridCP\Proxmox\Vm\Application\Service\DeleteVmService;
use GridCP\Proxmox\Vm\Domain\Exception\VmNotExitsException;
use GridCP\Proxmox\Vm\Domain\Repository\IVmRepository;
use GridCP\Proxmox\Vm\Domain\VO\VmUuid;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmEntity;
use Psr\Log\LoggerInterface;

class ListDeleteVmTest extends TestCase
{
    protected Generator $faker;
    protected IVmRepository $vmRepository;
    protected LoggerInterface $logger;

    protected DeleteVmService $delete;
    protected VmUuid $uuid;

    public function setUp(): void
    {
        $this->vmRepository = $this->getMockBuilder(IVmRepository::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->delete = new DeleteVmService($this->vmRepository, $this->logger);
        $this->faker = FakerFactory::create();
        $this->uuid = new VmUuid($this->faker->uuid());

    }

    public function testVmDeleteOK():void
    {
        $vmEntity = new VmEntity();
        $vmEntity->setId(1);
        $vmEntity->setUuid($this->uuid->value());
        $vmEntity->setActive(true);  

        $this->vmRepository->expects($this->any())
            ->method('findByUuid')
            ->with($this->uuid->value())
            ->willReturn($vmEntity);

        $this->delete->__invoke($this->uuid->value());
        $this->expectNotToPerformAssertions();
    }
    public function testDeleteVmByUUIDNotExist():void
    {
        $this->vmRepository->expects($this->any())
                           ->method('findByUuid')
                           ->with($this->uuid->value())
                           ->willReturn(null);

        $this->expectException(VmNotExitsException::class);
        $this->delete->__invoke($this->uuid->value());
    }
    // php bin/phpunit tests/Proxmox/Vm/Application/Service/ListPatchVmTest.php
}