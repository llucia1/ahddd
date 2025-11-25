<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeEntity;

/**
 * @extends ServiceEntityRepository<Ip4FloatGroupEntity>
 *
 *
 * @method Ip4FloatGroupEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ip4FloatGroupEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ip4FloatGroupEntity[]    findAll()
 * @method Ip4FloatGroupEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IpFloatGroupsRepository extends ServiceEntityRepository implements IIp4FloatGroupRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ip4FloatGroupEntity::class);
        
    }

    public function save(Ip4FloatGroupEntity $floatGroup): void
    {
        $this->_em->persist($floatGroup);
        $this->_em->flush();
    }

    /**
     * Make delete operation soft delete.
     *
     * @param Ip4FloatGroupEntity $floatGroup
     */
    public function delete(Ip4FloatGroupEntity $floatGroup): void
    {
        $floatGroup->active = false;
        $this->_em->persist($floatGroup);
        $this->_em->flush();
    }
    /**
     * Gets all networks associated with a floating group UUID.
     *
     * @param string $floatGroupUuid
     * @return array
     */
    public function findNetworksByFloatGroupUuid(string $floatGroupUuid): array
    {
        $dql = "
    SELECT n
    FROM GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity n
    JOIN GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkFloatGroupEntity ng WITH n.id = ng.network
    JOIN GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity fg WITH fg.id = ng.floatGroup
    WHERE fg.uuid = :floatGroupUuid
    AND fg.active = true
    AND ng.active = true
    AND n.active = true
    ";
    
    $query = $this->getEntityManager()->createQuery($dql);
    $query->setParameter('floatGroupUuid', $floatGroupUuid);
    
    return $query->getResult();
    }
    public function getAll(): array
    {
        return $this->findAll();
    }

    public function getByUuid(string $uuid): Ip4FloatGroupEntity|null
    {
        return $this->findOneBy(['uuid'=>$uuid, 'active'=>true]);
    }
    public function getByUuidWithNetworks(string $uuid): ?Ip4FloatGroupEntity
    {
        $entityManager = $this->getEntityManager();

        $floatGroup = $this->findOneBy(['uuid' => $uuid,'active'=>true]);

        if (!$floatGroup) {
            return null;
        }

        $query = $entityManager->createQuery(
            'SELECT n.uuid AS network_uuid, n.name AS network_name
            FROM GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkFloatGroupEntity nf
            JOIN GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity n WITH nf.network = n.id
            WHERE nf.floatGroup = :floatgroupId AND nf.active = :active'
        )->setParameter('floatgroupId', $floatGroup->getId())
         ->setParameter('active', true);
    
        $networkResults = $query->getResult();

        $networksCollection = new ArrayCollection();
        foreach ($networkResults as $result) {
            $network = new Ip4NetworkEntity();
            $network->setUuid($result['network_uuid']);
            $network->setName($result['network_name']);
            
            $networksCollection->add($network);
        }
        $floatGroup->setNetworks($networksCollection);
     
         return $floatGroup;
    }
    public function getByUuidWithRelations(string $uuid): ?Ip4FloatGroupEntity
    {
        return $this->createQueryBuilder('fg')

        ->leftJoin('fg.networkFloatGroups', 'nfg_net', 'WITH', 'nfg_net.active = true')
        ->leftJoin('nfg_net.network', 'net', 'WITH', 'net.active = true')
        ->addSelect('nfg_net', 'net')

        ->leftJoin('fg.nodeFloatGroups', 'nfg_node', 'WITH', 'nfg_node.active = true')
        ->leftJoin('nfg_node.node', 'n', 'WITH', 'n.active = true')
        ->addSelect('nfg_node', 'n')

        ->where('fg.uuid = :uuid')
        ->andWhere('fg.active = true')
        ->andWhere('(nfg_net.id IS NULL OR nfg_net.active = true)')
        ->andWhere('(net.id IS NULL OR net.active = true)')
        ->andWhere('(nfg_node.id IS NULL OR nfg_node.active = true)')
        ->andWhere('(n.id IS NULL OR n.active = true)')

        ->setParameter('uuid', $uuid)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function getByName(string $name): Ip4FloatGroupEntity|null
    {
        return $this->findOneBy(['name'=>$name, 'active'=>true]);
    }
    public function getAllActive(): array
    {
        return $this->findBy(['active' => true]);
    }
    public function getAllWithRelations(): array
    {
        return $this->createQueryBuilder('fg')
        ->select('fg')

        ->leftJoin('fg.networkFloatGroups', 'nfg_net', 'WITH', 'nfg_net.active = true')
        ->leftJoin('nfg_net.network', 'net', 'WITH', 'net.active = true')
        ->addSelect('nfg_net', 'net')

        ->leftJoin('fg.nodeFloatGroups', 'nfg_node', 'WITH', 'nfg_node.active = true')
        ->leftJoin('nfg_node.node', 'n', 'WITH', 'n.active = true AND n IS NOT NULL') 
        ->addSelect('nfg_node', 'n')

        ->where('fg.active = true')

        ->orderBy('fg.id', 'ASC')

        ->getQuery()
        ->getResult();
    }
}
