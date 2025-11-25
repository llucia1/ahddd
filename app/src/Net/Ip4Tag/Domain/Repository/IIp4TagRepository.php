<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Domain\Repository;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4TagEntity;

interface IIp4TagRepository
{
    public function save(Ip4TagEntity $ip4): void;
    public function findByuuid(string $uuid): ?Ip4TagEntity;
    public function findById(int $id): ?Ip4TagEntity;

    public function findByUuidWithIp(string $uuid): ?Ip4TagEntity;
}