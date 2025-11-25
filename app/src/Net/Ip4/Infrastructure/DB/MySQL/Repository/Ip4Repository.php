<?php

namespace GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

use GridCP\Net\Ip4\Domain\Exceptions\Ip4Duplicated;
use GridCP\Net\Ip4\Domain\Exceptions\ip4ErrorInsertBD;
use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkFloatGroupEntity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4TagEntity;

/**
 *
 * @extends ServiceEntityRepository<Ip4Entity>
 *
 * @method Ip4Entity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ip4Entity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ip4Entity[]    findAll()
 * @method Ip4Entity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */


class Ip4Repository extends  ServiceEntityRepository implements IIp4Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ip4Entity::class);
    }

    public function existIdNetwork(string $idNetwork): ?Ip4NetworkEntity
    {
        $exitsIdNetwork = $this->getEntityManager()->getRepository(Ip4NetworkEntity::class)->findOneBy( ['uuid'=>$idNetwork, 'active' => true] );
        if (!$exitsIdNetwork) {
            return null;
        }
        return $exitsIdNetwork;
    }

    public function saveAll(array $ip4s, int $idNetwork): array
    {
        $result = [];
        foreach ($ip4s as $ip4) {
            try {
                
                $ip4Entity = new Ip4Entity();
                $ip4Entity->setUuid($idNetwork);
                $ip4Entity->setIp($ip4);
                $ip4Entity->setActive(true);
                $this->save($ip4Entity);
    
                $result[] = $ip4Entity;
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                throw new Ip4Duplicated($ip4);
            } catch (\Exception $e) {
                throw new ip4ErrorInsertBD($ip4, $e->getMessage());
            }

        }
        return $result;
    }
    public function getIpsByNetworkIds(array $networkIds): array
    {
        if (empty($networkIds)) {
            return [];
        }
    
        return $this->createQueryBuilder('ip4')
        ->select('ip4, net') 
        ->innerJoin('ip4.network', 'net')
        ->where('net.id IN (:networkIds) AND ip4.active = true')
        ->setParameter('networkIds', $networkIds)
        ->getQuery()
        ->getResult();
    }
    public function save(Ip4Entity $ip4): void
    {
        $this->getEntityManager()->persist($ip4);
        $this->getEntityManager()->flush();
    }

    public function getAll(): array
    {
        $ips = $this->createQueryBuilder('i')
        ->leftJoin('i.network', 'n')
        ->addSelect('n')
        ->where('i.active = true')
        ->getQuery()
        ->getResult();

    return array_filter($ips, function($ip) {
        if (!$ip->getNetwork() || !$ip->getNetwork()->isActive()) {
            $ip->setNetwork(null);
        }
        return $ip->isActive();
    });
                

    }

    public function findByUuid(string $uuid): ?Ip4Entity
    {
        $ip = $this->createQueryBuilder('i')
                    ->leftJoin('i.network', 'n')
                    ->addSelect('n')
                    ->andWhere('i.uuid = :uuid')
                    ->andWhere('i.active = true')
                    ->setParameter('uuid', $uuid)
                    ->getQuery()
                    ->getOneOrNullResult();
        if (!$ip) {
            return null;
        }
        if (!$ip->getNetwork() || !$ip->getNetwork()->isActive()) {
            $ip->setNetwork(null);
        }
        return $ip;
    }

    public function findByIP(string $ip): ?Ip4Entity
    {
        return $this->findOneBy(['ip'=>$ip,'active'=>true]);
    }
    public function findByIPWhitRelationsNetworksTags(string $ip): ?Ip4Entity
    {
        return $this->createQueryBuilder('ip')
            ->leftJoin('ip.network', 'network', 'WITH', 'network.active = true')
            ->addSelect('network')
            ->where('ip.ip = :ip')
            ->andWhere('ip.active = true')
            ->setParameter('ip', $ip)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findByIPWithRelations(string $ip): ?array
    {
        $entityManager = $this->getEntityManager();
        
        $rsm = new ResultSetMapping();
    
        $rsm->addScalarResult('ip_id', 'ip_id');
        $rsm->addScalarResult('ip_uuid', 'ip_uuid');
        $rsm->addScalarResult('ip_address', 'ip_address');
        $rsm->addScalarResult('ip_active', 'ip_active');
        $rsm->addScalarResult('network_id', 'network_id');
        $rsm->addScalarResult('network_uuid', 'network_uuid');
        $rsm->addScalarResult('network_name', 'network_name');
        $rsm->addScalarResult('network_netmask', 'network_netmask');
        $rsm->addScalarResult('network_gateway', 'network_gateway');
        $rsm->addScalarResult('network_active', 'network_active');
        $rsm->addScalarResult('floatgroup_id', 'floatgroup_id');
        $rsm->addScalarResult('floatgroup_uuid', 'floatgroup_uuid');
        $rsm->addScalarResult('floatgroup_name', 'floatgroup_name');
        $rsm->addScalarResult('floatgroup_active', 'floatgroup_active');
        $rsm->addScalarResult('ip_tag', 'ip_tag');

        $query = $entityManager->createNativeQuery("
            SELECT
                ip.id AS ip_id,
                ip.uuid AS ip_uuid,
                ip.ip AS ip_address,
                ip.active AS ip_active,
                tag.tag AS ip_tag,
                net.id AS network_id,
                net.uuid AS network_uuid,
                net.name AS network_name,
                net.netmask AS network_netmask,
                net.gateway AS network_gateway,
                net.active AS network_active,
                fg.id AS floatgroup_id,
                fg.uuid AS floatgroup_uuid,
                fg.name AS floatgroup_name,
                fg.active AS floatgroup_active
            FROM
                ip4 ip
            LEFT JOIN 
                ip4_tag tag ON tag.id_ip = ip.id AND tag.active = 1
            LEFT JOIN
                ip4_network net ON ip.id_network = net.id AND net.active = 1
            LEFT JOIN
                ip4_network_float_gorup nfg ON net.id = nfg.network_id AND nfg.active = 1
            LEFT JOIN
                ip4_float_group fg ON nfg.floatgroup_id = fg.id AND fg.active = 1
            WHERE
                ip.ip = :ip
                AND ip.active = 1
        ", $rsm);
    
        $query->setParameter('ip', $ip);
    
        $results = $query->getResult();
    
        if (empty($results)) {
            return [];
        }

        $data = [
            'ip' => [
                'id' => $results[0]['ip_id'],
                'uuid' => $results[0]['ip_uuid'],
                'address' => $results[0]['ip_address'],
                'active' => $results[0]['ip_active'],
                "tag" => $results[0]['ip_tag'],
            ],
            'network' => [
                'id' => $results[0]['network_id'],
                'uuid' => $results[0]['network_uuid'],
                'name' => $results[0]['network_name'],
                'mask' => $results[0]['network_netmask'],
                'gateway' => $results[0]['network_gateway'],
                'active' => $results[0]['network_active'],
                'floatGroups' => [],
            ],
        ];
    
        foreach ($results as $result) {
            if (!empty($result['floatgroup_id'])) {
                $data['network']['floatGroups'][] = [
                    'id' => $result['floatgroup_id'],
                    'uuid' => $result['floatgroup_uuid'],
                    'name' => $result['floatgroup_name'],
                    'active' => $result['floatgroup_active'],
                ];
            }
        }
    
        return $data;
    }

    public function findAllByNetworkid(int $networkId): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.network', 'n')
            ->addSelect('n')
            ->leftJoin('i.tags', 't')
            ->addSelect('t')
            ->where('n.id = :networkId')
            ->andWhere('i.active = true')
            ->setParameter('networkId', $networkId)
            ->getQuery()
            ->getResult();
    }
    
    public function delete(string $uuid): bool
    {
        $ipEntity = $this->findByUuid($uuid);

        if ($ipEntity === null) {
            return false;
        }

        $this->getEntityManager()->remove($ipEntity);
        $this->getEntityManager()->flush();

        return true;
    }
    
    public function deleteByIp(string $ip): bool
    {
        $ipEntity = $this->findByIP($ip);

        if ($ipEntity === null) {
            return false;
        }

        $ipEntity->setActive(false);
        $this->save($ipEntity);


        $ip4Tag = $this->getEntityManager()
            ->getRepository(Ip4TagEntity::class)
            ->findOneBy(['ip' => $ipEntity, 'active' => true]);

        if ($ip4Tag !== null) {
            $ip4Tag->setActive(false);
            $this->getEntityManager()->persist($ip4Tag);
        }

        $this->getEntityManager()->flush();

        return true;
    }


    public function getIpsNotAssignedToAnyVm(array $ips): array
    {
        $qb = $this->createQueryBuilder('ip4')
            ->where('ip4.active = true')
            ->andWhere('ip4.ip IN (:ips)')
            ->andWhere('ip4.id IN (
                SELECT IDENTITY(v.ip4)
                FROM GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmIp4Entity v
                WHERE v.active = true
            )')
            ->setParameter('ips', $ips);
        
        return $qb->getQuery()->getResult();
    }

    public function findByUuidWithActiveTag(string $uuid): ?Ip4Entity
    {
        return $this->createQueryBuilder('ip')
            ->leftJoin('ip.tags', 'tag', 'WITH', 'tag.active = true')
            ->addSelect('tag')
            ->where('ip.uuid = :uuid')
            ->andWhere('ip.active = true')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function getAllWithActiveTagAndNetwork(): array
    {
        return $this->createQueryBuilder('ip')
            ->leftJoin('ip.tags', 'tag', 'WITH', 'tag.active = true')
            ->leftJoin('ip.network', 'network', 'WITH', 'network.active = true')
            ->addSelect('tag', 'network')
            ->where('ip.active = true')
            ->getQuery()
            ->getResult();
    }
}
