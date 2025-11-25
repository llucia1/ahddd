<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Domain\Repository;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkFloatGroupEntity;

interface IIp4NetworkFloatGroupRepository
{

    public function save(Ip4NetworkFloatGroupEntity $network): void;
    public function delete(Ip4NetworkFloatGroupEntity $network): void;
    public function getAll():array;
    public function getByIdNetwork(int $idNetwork): ?Ip4NetworkFloatGroupEntity;
    public function getByIdFloatGroup(int $idFloatGroup): ?Ip4NetworkFloatGroupEntity;
    public function byIdNetwork(int $idNetwork): ?Ip4NetworkFloatGroupEntity;
}