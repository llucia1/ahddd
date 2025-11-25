<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Domain\Repository;

use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;

interface IIp4SubnetRepository
{
    public function getAll(): array;
    public function getAllWithRelation(): array;
    public function findByUuid(string $ip): ?Ip4SubnetEntity;
    public function findByUuidWithRelations(string $uuid): array | bool;
    public function save(Ip4SubnetEntity $subnet): void;
    public function findSubnetContainingIp(string $ip): ?Ip4SubnetEntity;
    public function getAllWithRelationsByUuidClient(?string $uuidClient): array;
    public function getAllSubnetsByFloatgroupUuid(string $floatgroupUuid): array;
    public function findBySubnet(string $ip, int $mask): array;
}