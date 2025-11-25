<?php
declare(strict_types=1);
namespace GridCP\Device\Domain\Repository;

use GridCP\Device\Domain\VO\Device;
use GridCP\Device\Infrastructure\DB\MySQL\Entity\DeviceEntity;

interface IDeviceRepository
{
    public function save(DeviceEntity $device): void;
    public function delete(DeviceEntity $device): void;
    public function findOneByData(Device $data): ?DeviceEntity;
    public function findOneByIp(string $ip): ?DeviceEntity;
    public function findByUuid(string $uuid): ?DeviceEntity;
    public function getAll(): array;

}