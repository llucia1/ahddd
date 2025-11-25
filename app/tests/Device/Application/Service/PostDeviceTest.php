<?php
declare(strict_types=1);

namespace Device\Application\Service;

use Exception;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\Bus\EventSource\EventBus;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Device\Application\Service\CreateDeviceService;
use GridCP\Device\Domain\VO\Device;
use GridCP\Device\Domain\VO\DeviceCountry;
use GridCP\Device\Domain\VO\DeviceDevice;
use GridCP\Device\Domain\VO\DeviceIp;
use GridCP\Device\Domain\VO\DeviceLocation;
use GridCP\Device\Domain\VO\DeviceUuid;
use GridCP\Device\Infrastructure\DB\MySQL\Entity\DeviceEntity;
use GridCP\Device\Infrastructure\DB\MySQL\Repository\DeviceRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostDeviceTest extends TestCase
{
    protected Generator $faker;

    protected DeviceRepository $deviceRepository;

    private CreateDeviceService $deviceService;

    private Device $device;

    public function setUp(): void
    {
        $eventBus = $this->createMock(EventBus::class);

        $this->deviceRepository = $this->getMockBuilder( DeviceRepository::class )->disableOriginalConstructor()->getMock();
        $this->deviceService = new CreateDeviceService($this->deviceRepository, $eventBus);
        $this->faker = FakerFactory::create();

        $deviceUuid = new DeviceUuid( UuidValueObject::random()->value() );
        $deviceDevice = new DeviceDevice( $this->faker->userAgent() );
        $deviceIp = new DeviceIp( $this->faker->ipv4() );
        $deviceCountry = new DeviceCountry( $this->faker->countryCode() );
        $deviceLocation = new DeviceLocation( $this->faker->city() );


        $this->device = new Device(
                                    $deviceUuid,
                                    $deviceIp,
                                    $deviceDevice,
                                    $deviceCountry,
                                    $deviceLocation
                                );
                                
    }


    private function createDeviceEntity(Device $device): DeviceEntity
    {
        $deviceEntity = new DeviceEntity();
        $deviceEntity->setUuid($device->uuid()->value());
        $deviceEntity->setDevice($device->device()->value());
        $deviceEntity->setIp($device->ip()->value());
        $deviceEntity->setCountry($device->country()->value());
        $deviceEntity->setLocation($device->location()->value());
        return $deviceEntity;
    }

    public function testPostDeviceSuccess(): void
    {

        $deviceEntity = $this->createDeviceEntity($this->device);

        $this->deviceRepository->expects($this->once())
            ->method('findOneByData')
            ->with($this->device)
            ->willReturn(null);

        $this->deviceRepository->expects($this->once())
            ->method('save')
            ->with($deviceEntity);

        $uuid = $this->deviceService->__invoke($this->device);
        $this->assertEquals($uuid, $deviceEntity->getUuid());
        
    }
    
    public function testCreateIp4Conflict()
    {
        $this->deviceRepository->expects($this->once())
            ->method('findOneByData')
            ->with($this->device)
            ->willReturn($this->createDeviceEntity($this->device));
        
            try {
                $this->deviceService->__invoke($this->device);
            } catch (HttpException $e) {
                $this->assertSame(Response::HTTP_CONFLICT, $e->getStatusCode());
                $this->assertSame('Device duplicate', $e->getMessage());
                return;
            }
        
    }

}