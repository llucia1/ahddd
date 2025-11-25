<?php
declare(strict_types=1);

namespace GridCP\Device\Application\Service;

use Exception;
use GridCP\Common\Domain\Bus\EventSource\EventBus;
use GridCP\Device\Domain\Repository\IDeviceAuthRepository;
use GridCP\Device\Domain\Repository\IDeviceRepository;
use GridCP\Device\Domain\Service\ICreateDeviceAuthService;
use GridCP\Device\Domain\Service\IPostModifyDeviceService;
use GridCP\Device\Domain\VO\Device;
use GridCP\Device\Infrastructure\DB\MySQL\Entity\DeviceEntity;
use GridCP\Device\Infrastructure\DB\MySQL\Entity\DevicesAuthEntity;
use GridCP\Security\Common\Infrastructure\DB\MySQL\Entity\AuthEntity;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateDeviceAuthService implements ICreateDeviceAuthService
{
    public function __construct(
        private readonly IDeviceRepository $deviceRepository,
        private readonly IDeviceAuthRepository $deviceAuthRepository,
        private readonly LoggerInterface  $logger,
        private readonly EventBus       $bus
    ) {}

    public function __invoke(string $uuidDevice, AuthEntity $authEntity): bool
    {
        return $this->setAuthDevice($uuidDevice, $authEntity);
    }


    function setAuthDevice( string $uuidDevice, AuthEntity $authEntity): bool
    { 
        $this->logger->info("Init insert AuthDevice service for -> " . $authEntity->getEmail());
        $device = $this->deviceRepository->findByUuid( $uuidDevice );
        if (!$device) {
            throw new HttpException(Response::HTTP_CONFLICT, 'No exist Device in insert AuthDevice');
        }
        
        $deviceAuthEntity = new DevicesAuthEntity(); 
        $deviceAuthEntity->setDeviceId($device->getId() );
        $deviceAuthEntity->setAuthId($authEntity->getId() );
        $deviceAuthEntity->setActive(true);
        try {
            $this->deviceAuthRepository->save($deviceAuthEntity);
            //$this->bus->publish(...$device->pullDomainEvents());
        } catch (Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error save AuthDevice' . $e->getMessage());
        }
        
        return true;
    }
}