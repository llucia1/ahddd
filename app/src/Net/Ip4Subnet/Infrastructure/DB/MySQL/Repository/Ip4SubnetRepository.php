<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use Doctrine\ORM\Query\Expr\Join;
/**
 * @extends ServiceEntityRepository<Ip4SubnetEntity>
 *
 * @implements IIp4SubnetRepository<Ip4SubnetEntity>
 *
 * @method Ip4SubnetEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ip4SubnetEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ip4SubnetEntity[]    findAll()
 * @method Ip4SubnetEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Ip4SubnetRepository extends ServiceEntityRepository implements IIp4SubnetRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ip4SubnetEntity::class);
    }
    public function findByUuid(string $uuid): ?Ip4SubnetEntity
    {
        $queryBuilder = $this->createQueryBuilder('subnet')
        ->leftJoin('subnet.propertySubnet', 'property')
        ->addSelect('property')
        ->where('subnet.uuid = :uuid')
        ->andWhere('subnet.active = true') // Solo subnets activas
        ->setParameter('uuid', $uuid);

    return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findByUuidWithRelations(string $uuid): array | bool
    {
        $sql = "
            SELECT
                subnet.id AS subnet_id,
                subnet.uuid AS subnet_uuid,
                subnet.ip AS subnet_ip,
                subnet.mask AS subnet_mask,
                subnet.active AS subnet_active,
                subnet.created_at AS subnet_created_at,
                subnet.updated_at AS subnet_updated_at,
                subnet.uuid_floatgroup AS subnet_uuid_floatgroup,
                floatgroup.uuid AS floatgroup_uuid,
                floatgroup.name AS floatgroup_name,
                owner.id AS owner_id,
                owner.subnet_id AS owner_subnet_id,
                owner.client_uuid AS owner_client_uuid,
                client.uuid AS client_uuid,
                client.name AS client_name
            FROM ip4_subnet subnet
            LEFT JOIN ip4_float_group floatgroup
                ON subnet.uuid_floatgroup = floatgroup.uuid
                AND floatgroup.active = 1
            LEFT JOIN ip4_subnet_owner owner
                ON owner.subnet_id = subnet.id
                AND owner.active = 1
            LEFT JOIN clients client
                ON owner.client_uuid = client.uuid
                AND client.active = 1
            WHERE subnet.uuid = :uuid
              AND subnet.active = 1;
        ";
    
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $rows = $stmt->executeQuery(['uuid' => $uuid])->fetchAllAssociative();
    
        if (empty($rows)) {
            return false;
        }
    
        $result = [
            'id' => null,
            'uuid' => null,
            'ip' => null,
            'mask' => null,
            'active' => null,
            'created_at' => null,
            'updated_at' => null,
            'uuid_floatgroup' => null,
            'floatgroup_uuid' => null,
            'floatgroup_name' => null,
            'subnet_id' => null,
            'client_uuid' => null,
            'client_name' => null,
        ];
    
        foreach ($rows as $row) {
            if (!$result['id']) {
                $result['id'] = $row['subnet_id'];
                $result['uuid'] = $row['subnet_uuid'];
                $result['ip'] = $row['subnet_ip'];
                $result['mask'] = $row['subnet_mask'];
                $result['active'] = $row['subnet_active'];
                $result['created_at'] = $row['subnet_created_at'];
                $result['updated_at'] = $row['subnet_updated_at'];
                $result['uuid_floatgroup'] = $row['subnet_uuid_floatgroup'];
            }
    
            if (!$result['floatgroup_uuid'] && $row['floatgroup_uuid']) {
                $result['floatgroup_uuid'] = $row['floatgroup_uuid'];
                $result['floatgroup_name'] = $row['floatgroup_name'];
            }
    
            if (!$result['client_uuid'] && $row['client_uuid']) {
                $result['subnet_id'] = $row['owner_subnet_id'];
                $result['client_uuid'] = $row['client_uuid'];
                $result['client_name'] = $row['client_name'];
            }
        }
    
        return $result;
    }

    public function getAllWithRelation(): array
    {
        
        $sql = "
            SELECT
                subnet.id AS subnetId,
                subnet.uuid AS subnetUuid,
                subnet.ip AS subnetIp,
                subnet.mask AS subnetMask,
                floatGroup.id AS floatGroupId,
                floatGroup.uuid AS floatGroupUuid,
                floatGroup.name AS floatGroupName,
                -- Obtener los datos del cliente solo si Property.active = 1
                MAX(CASE WHEN property.active = 1 THEN client.id ELSE NULL END) AS clientId,
                MAX(CASE WHEN property.active = 1 THEN client.uuid ELSE NULL END) AS clientUuid,
                MAX(CASE WHEN property.active = 1 THEN client.name ELSE NULL END) AS clientName
            FROM
                ip4_subnet subnet
            LEFT JOIN
                ip4_subnet_owner property ON property.subnet_id = subnet.id
            LEFT JOIN
                ip4_float_group floatGroup ON subnet.uuid_floatgroup = floatGroup.uuid
            LEFT JOIN
                clients client ON property.client_uuid = client.uuid
            WHERE
                subnet.active = 1
            GROUP BY
                subnet.id, subnet.uuid, subnet.ip, subnet.mask, floatGroup.id, floatGroup.uuid, floatGroup.name
        ";

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($sql);

        return $statement->executeQuery()->fetchAllAssociative();
    }
    public function getAll(): array
    {
        return $this->findBy(['active' => true]);
    }
    public function findBySubnet(string $ip, int $mask): array
    {
        return $this->findBy(['ip' => $ip, 'mask' => $mask,'active' => true]);
    }
    public function getAllSubnetsByFloatgroupUuid(string $floatgroupUuid): array
    {
        return $this->findBy(['uuidFloatgroup' => $floatgroupUuid, 'active' => true]);
    }

    public function getAllWithRelationsByUuidClient(?string $uuidClient): array
    {
        
        $sql = "
            SELECT DISTINCT
                subnet.id AS subnetId,
                subnet.uuid AS subnetUuid,
                subnet.ip AS subnetIp,
                subnet.mask AS subnetMask,
                floatGroup.id AS floatGroupId,
                floatGroup.uuid AS floatGroupUuid,
                floatGroup.name AS floatGroupName,
                client.id AS clientId,
                client.uuid AS clientUuid,
                client.name AS clientName
            FROM
                ip4_subnet subnet
            LEFT JOIN
                ip4_float_group floatGroup ON subnet.uuid_floatgroup = floatGroup.uuid
            LEFT JOIN
                ip4_subnet_owner property ON property.subnet_id = subnet.id
            LEFT JOIN
                clients client ON property.client_uuid = client.uuid
            WHERE
                subnet.active = 1
                AND (
                    " . ($uuidClient === null
                        ? "(property.id IS NULL OR (property.active = 1 AND property.client_uuid IS NULL))"
                        : "(property.active = 1 AND property.client_uuid = :uuidClient)") . "
                )
        ";
        
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($sql);
        
        if ($uuidClient !== null) {
            $statement->bindValue('uuidClient', $uuidClient);
        }
    
        return $statement->executeQuery()->fetchAllAssociative();



    }
    public function save(Ip4SubnetEntity $subnet): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($subnet);
        $entityManager->flush();
    }
    public function findSubnetContainingIp(string $ip): ?Ip4SubnetEntity
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT *
            FROM ip4_subnet
            WHERE INET_ATON(:ip) BETWEEN INET_ATON(ip) AND (INET_ATON(ip) + POW(2, (32 - mask)) - 1)
              AND active = 1
            LIMIT 1
        ';
    
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('ip', $ip);
        $result = $stmt->executeQuery()->fetchAssociative();
    
        if (!$result) {
            return null;
        }
    

        return $this->getEntityManager()->getRepository(Ip4SubnetEntity::class)->find($result['id']);
        
    }
}
