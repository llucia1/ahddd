<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use GridCP\Net\Ip4Network\Domain\Repository\IIp4NetworkFloatGroupRepository;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkFloatGroupEntity;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorFloatGroupOrNetworkNotExist;


class Ip4NetworkFloatGroupRepository extends ServiceEntityRepository implements IIp4NetworkFloatGroupRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ip4NetworkFloatGroupEntity::class);
    }


    public function save(Ip4NetworkFloatGroupEntity $network): void
    {


            try {
                $entityManager = $this->getEntityManager();
                $entityManager->persist($network);
                $entityManager->flush();
            }catch(ForeignKeyConstraintViolationException $e){
                throw new ErrorFloatGroupOrNetworkNotExist((string)$e->getMessage());
            }
    }

    public function delete(Ip4NetworkFloatGroupEntity $network): void
    {
        $entityManager = $this->getEntityManager();
        $network->setActive(false);
        $entityManager->persist($network);
        $entityManager->flush();
    }

    public function getAll(): array
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository(Ip4NetworkFloatGroupEntity::class)->findBy(["active"=>true]);
    }


    public function getByIdNetwork(int $idNetwork): ?Ip4NetworkFloatGroupEntity
    {
        return $this->findOneBy(["network" => $idNetwork, "active" => true]);
    }
    public function getByIdFloatGroup(int $idFloatGroup): ?Ip4NetworkFloatGroupEntity
    {
        $entityManger = $this->getEntityManager();
        return $entityManger->getRepository(Ip4NetworkFloatGroupEntity::class)->findOneBy(["floatgroup_id"=>$idFloatGroup,"active"=>true]);

    }
    public function byIdNetwork(int $idNetwork): ?Ip4NetworkFloatGroupEntity
    {
        $entityManger = $this->getEntityManager();
        return $entityManger->getRepository(Ip4NetworkFloatGroupEntity::class)->findOneBy(["network_id"=>$idNetwork]);
    }
}
