<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Domain\Repository;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;

interface IIp4FloatGroupRepository
{
    function save(Ip4FloatGroupEntity $floatGroup): void;
    function delete(Ip4FloatGroupEntity $floatGroup): void;
    function getAll():array;
    function getByUuid(string $uuid):Ip4FloatGroupEntity|null;
    function getByName(string $name):Ip4FloatGroupEntity|null;
    function getAllActive(): array;
    public function getByUuidWithNetworks(string $uuid): ?Ip4FloatGroupEntity;
    public function getByUuidWithRelations(string $uuid): ?Ip4FloatGroupEntity;
    public function findNetworksByFloatGroupUuid(string $floatGroupUuid): array;
    public function getAllWithRelations(): array;


}