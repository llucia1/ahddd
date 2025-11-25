<?php
declare(strict_types=1);
namespace GridCP\Device\Domain\Repository;


use GridCP\Device\Infrastructure\DB\MySQL\Entity\DevicesAuthEntity;

interface IDeviceAuthRepository
{
    public function findOneById(string $id): ?DevicesAuthEntity;
    public function save(DevicesAuthEntity $devicesAuth): void;

    public function findDeviceByAuthId(int $id):?array;
}