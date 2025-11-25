<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;

/**
 * @extends ServiceEntityRepository<Ip4SubnetOwnerEntity>
 *
 * @implements IIp4SubnetOwnerRepository<Ip4SubnetOwnerEntity>
 *
 * @method PropertySubnetIp4|null findByUserUuid(string $userId)
 * @method PropertySubnetIp4|null findBySubnetUuid(string $subnetId)
 *
 */
class Ip4SubnetOwnerRepository extends ServiceEntityRepository implements IIp4SubnetOwnerRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ip4SubnetOwnerEntity::class);
    }
    public function findByUserId(int $userId): ?Ip4SubnetOwnerEntity
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'user')
            ->andWhere('user.id = :userId')
            ->andWhere('p.active = true')// NOSONAR
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findBySubnetUuid(string $subnetUuid): ?Ip4SubnetOwnerEntity
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.subnet', 'subnet')
            ->andWhere('subnet.uuid = :subnetUuid')
            ->andWhere('p.active = true')// NOSONAR
            ->setParameter('subnetUuid', $subnetUuid)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function getAllSubnetsByClientUuid(?string $clientUuid): array
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
                client.id AS clientId,
                client.uuid AS clientUuid,
                client.name AS clientName
            FROM
                property_ip4_subnet property
            INNER JOIN
                ip4_subnet subnet ON property.subnet_id = subnet.id
            LEFT JOIN
                ip4_float_group floatGroup ON subnet.uuid_floatgroup = floatGroup.uuid
            LEFT JOIN
                clients client ON property.client_uuid = client.uuid
            WHERE
                property.active = 1
                AND subnet.active = 1
                " . ($clientUuid !== null
                    ? "AND property.client_uuid = :clientUuid"
                    : "AND (property.client_uuid IS NULL OR property.client_uuid IS NOT NULL)");

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($sql);

        if ($clientUuid !== null) {
            $statement->bindValue('clientUuid', $clientUuid);
        }

        return $statement->executeQuery()->fetchAllAssociative();
    }


    public function save(Ip4SubnetOwnerEntity $propertySubnet): void
    {
        
        $entityManager = $this->getEntityManager();
    
        $entityManager->persist($propertySubnet);
        $entityManager->flush();
    }
}
