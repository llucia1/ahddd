<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkFloatGroupEntity;
use GridCP\Net\Ip4Network\Domain\Repository\IIp4NetworkRepository;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;

/**
 * @extends ServiceEntityRepository<Ip4NetworkEntity>
 *
 * @implements IIp4NetworkRepository<Ip4NetworkEntity>
 *
 * @method Ip4NetworkEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ip4NetworkEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ip4NetworkEntity[]    findAll()
 * @method Ip4NetworkEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Ip4NetworkRepository extends ServiceEntityRepository implements IIp4NetworkRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ip4NetworkEntity::class);
    }


    public function save(Ip4NetworkEntity $network): void
    {
            $entityManager = $this->getEntityManager();
            $entityManager->persist($network);
            $entityManager->flush();
    }

    public function delete(?string $network): void
    {
        $entityManager = $this->getEntityManager();
        $entity = $entityManager->getRepository(Ip4NetworkEntity::class)->findOneBy(["uuid"=>$network,"active"=>true]);
        $entity->setActive(false);
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    public function getAll(): ?array
    {
        $arrayIp4networks=[];
        $entityManager = $this->getEntityManager();
        $result = $entityManager->getRepository(Ip4NetworkEntity::class)->findBy(["active"=>true]);
        if (!$result) {
            return null;
        }
        foreach ($result as $item) {
           $ip4Network = $this->getByUuid($item->getUuid());
           array_push($arrayIp4networks, $ip4Network);
        }
        return $arrayIp4networks;
    }


    public function getByUuid(string $uuid): Ip4NetworkEntity|null
    {
        $entityManager = $this->getEntityManager();
        $resultNE = $entityManager->getRepository(Ip4NetworkEntity::class)->findOneBy(["uuid"=>$uuid,"active"=>true]);

        if (!$resultNE) {
            return null;
        }
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult(Ip4FloatGroupEntity::class, 'i4fg');
        $rsm->addFieldResult('i4fg','id','id');
        $rsm->addFieldResult('i4fg','uuid','uuid');
        $rsm->addFieldResult('i4fg','name','name');
        $query = $entityManager->createNativeQuery(
            "SELECT * FROM ip4_float_group  as i4fg left join ip4_network_float_gorup as nf on i4fg.id = nf.floatgroup_id where nf.network_id = :networkId and nf.active = :active", $rsm
        )->setParameter('networkId', $resultNE->getId())->setParameter('active', true);
        $networkResult = $query->getResult();
        if (!empty($networkResult)){ $resultNE->setFloatGroup($networkResult[0]);}
        return $resultNE;
    }

    public function getByName(string $name):Ip4NetworkEntity|null
    {
        $entityManger = $this->getEntityManager();
        return $entityManger->getRepository(Ip4NetworkEntity::class)->findOneBy(["name"=>$name,"active"=>true]);

    }
    public function getById(string $id): Ip4NetworkEntity|null
    {
        $entityManger = $this->getEntityManager();
        return $entityManger->getRepository(Ip4NetworkEntity::class)->findOneBy(["id"=>$id,"active"=>true]);
    }




    /**
     * Método para obtener las IPs asociadas a una red por su UUID
     *
     * @param string $networkUuid
     * @return Ip4Entity[]|null
     */
    public function findIpsByNetworkUuid(string $networkUuid): ?array
    {
        $network = $this->createQueryBuilder('n')
            ->andWhere('n.uuid = :uuid')
            ->setParameter('uuid', $networkUuid)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$network) {
            return null;
        }

        return $network->getIps()->toArray();
    }
}
