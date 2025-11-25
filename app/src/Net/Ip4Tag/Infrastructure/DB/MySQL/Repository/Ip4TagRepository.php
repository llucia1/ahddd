<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Infrastructure\DB\MySQL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4TagEntity;
use GridCP\Net\Ip4Tag\Domain\Exception\Ip4TagInsertErrorDBException;
use GridCP\Net\Ip4Tag\Domain\Repository\IIp4TagRepository;

/**
 * @extends ServiceEntityRepository<Ip4TagEntity>
 *
 * @implements IIp4TagRepository<Ip4TagEntity>
 *
 * @method Ip4TagEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ip4TagEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ip4TagEntity[]    findAll()
 * @method Ip4TagEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Ip4TagRepository extends ServiceEntityRepository implements IIp4TagRepository
{
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Ip4TagEntity::class);
        }

        public function save(Ip4TagEntity $tag): void
        {
            $entityManager = $this->getEntityManager();

            try {
                $entityManager->persist($tag);
                $entityManager->flush();
            } catch (Exception $e) {
                throw new Ip4TagInsertErrorDBException($e->getMessage());
            }
        }

        public function findByuuid(string $uuid): ?Ip4TagEntity
        {
            return $this->findOneBy(['uuid'=>$uuid,'active'=>true]);
        }

        public function findById(int $id): ?Ip4TagEntity
        {
            return $this->findOneBy(['ip'=>$id,'active'=>true]);
        }
        public function findByUuidWithIp(string $uuid): ?Ip4TagEntity
        {
            return $this->createQueryBuilder('tag')
                ->leftJoin('tag.ip', 'ip')
                ->addSelect('ip')
                ->where('tag.uuid = :uuid')
                ->andWhere('tag.active = true')
                ->andWhere('ip.active = true')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getOneOrNullResult();
        }

}