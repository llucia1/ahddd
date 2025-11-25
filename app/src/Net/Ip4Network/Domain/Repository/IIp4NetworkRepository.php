<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Domain\Repository;


use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;

interface IIp4NetworkRepository
{

    public function save(Ip4NetworkEntity $network): void;
    public function delete(?string $network): void;
    public function getAll():?array;
    public function getByUuid(string $uuid):Ip4NetworkEntity|null;
    public function getByName(string $name):Ip4NetworkEntity|null;
    public function getById(string $id): Ip4NetworkEntity|null;
    public function findIpsByNetworkUuid(string $networkUuid): ?array;
}