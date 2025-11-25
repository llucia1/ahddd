<?php
declare(strict_types=1);

namespace GridCP\Device\Application\Service;

use Exception;
use GridCP\Common\Domain\Bus\EventSource\EventBus;
use GridCP\Device\Domain\Repository\IDeviceRepository;
use GridCP\Device\Domain\Service\IPostDeviceService;
use GridCP\Device\Domain\VO\Device;
use GridCP\Device\Infrastructure\DB\MySQL\Entity\DeviceEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateDeviceService implements IPostDeviceService
{
    public function __construct(
        private readonly IDeviceRepository $deviceRepository,
        private readonly EventBus       $bus
    ) {}

    public function __invoke(Device $device): string
    {
        return $this->create($device);
    }


    function create(Device $device): string
    { 
        $existDevice = $this->deviceRepository->findOneByData( $device );
        if ($existDevice) {
            throw new HttpException(Response::HTTP_CONFLICT, 'Device duplicate');
        }
        
        $deviceEntity = new DeviceEntity(); 
        $deviceEntity->setUuid($device->uuid()->value());
        $deviceEntity->setDevice($device->device()->value());
        $deviceEntity->setIp($device->ip()->value());
        $deviceEntity->setCountry($device->country()->value()); 
        $deviceEntity->setLocation($device->location()->value());
        try {
            $this->deviceRepository->save($deviceEntity);
            //$this->bus->publish(...$device->pullDomainEvents());
        } catch (Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $e->getMessage());
        }
        
        return $deviceEntity->getUuid();
    }
}