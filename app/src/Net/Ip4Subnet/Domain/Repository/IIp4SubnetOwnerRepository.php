<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Domain\Repository;

use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;

interface IIp4SubnetOwnerRepository
{
    public function findByUserId(int $userId): ?Ip4SubnetOwnerEntity;
    public function findBySubnetUuid(string $subnetUuid): ?Ip4SubnetOwnerEntity;
    public function getAllSubnetsByClientUuid(?string $clientUuid): array;
    public function save(Ip4SubnetOwnerEntity $subnet): void;
}