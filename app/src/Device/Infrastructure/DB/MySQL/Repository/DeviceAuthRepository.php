<?php
declare(strict_types=1);

namespace GridCP\Device\Infrastructure\DB\MySQL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use GridCP\Device\Domain\Repository\IDeviceAuthRepository;
use GridCP\Device\Infrastructure\DB\MySQL\Entity\DevicesAuthEntity;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<DevicesAuthEntity>
 * @implements IDeviceAuthRepository<DevicesAuthEntity>
 */
class DeviceAuthRepository extends ServiceEntityRepository implements IDeviceAuthRepository
{
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, DevicesAuthEntity::class);
    }
    
    public function findOneById(string $id): ?DevicesAuthEntity
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findDeviceByAuthId(int $id):?array
    {
        try{

            $sql = "SELECT d.ip 
                    FROM devices_auth AS da
                    INNER JOIN device AS d on d.id =da.device_id
                    WHERE da.auth_id = :id
                    ORDER BY (d.created_at) DESC LIMIT 1";
            $conn = $this->getEntityManager()->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue('id', $id);
            $result = $stmt->executeQuery();
            return $result->fetchAllAssociative();
        }catch(\Exception $e){
            $this->logger->error('Error, UserRepository in findByAuthUuid: ' . $e->getMessage(), [
                'exception' => $e,
                'Id' => $id,
            ]);
            return [];
        }
    }

    public function save(DevicesAuthEntity $devicesAuth): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($devicesAuth);
        $entityManager->flush();
    }
}