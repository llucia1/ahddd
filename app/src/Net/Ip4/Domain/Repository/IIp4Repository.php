<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Domain\Repository;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;

interface IIp4Repository
{
    public function save(Ip4Entity $ip4):void;
    public function getAll(): array;
    public function findByUuid(string $uuid): ?Ip4Entity;
    public function findByIP(string $ip): ?Ip4Entity;
    public function existIdNetwork(string $idNetwork): ?Ip4NetworkEntity;
    public function getIpsByNetworkIds(array $networkIds): array;
    public function delete(string $uuid): bool;
    public function findAllByNetworkid(int $networkId): array;
    public function deleteByIp(string $ip): bool;
    public function findByIPWithRelations(string $ip): ?array;
    public function getIpsNotAssignedToAnyVm(array $ips): array;
    public function findByUuidWithActiveTag(string $uuid): ?Ip4Entity;
    public function getAllWithActiveTagAndNetwork(): array;
    public function findByIPWhitRelationsNetworksTags(string $ip): ?Ip4Entity;
}