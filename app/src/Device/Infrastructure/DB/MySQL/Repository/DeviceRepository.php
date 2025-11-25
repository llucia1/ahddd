<?php
declare(strict_types=1);

namespace GridCP\Device\Infrastructure\DB\MySQL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use GridCP\Device\Domain\Repository\IDeviceRepository;
use GridCP\Device\Domain\VO\Device;
use GridCP\Device\Infrastructure\DB\MySQL\Entity\DeviceEntity;
use phpDocumentor\Reflection\Location;

/**
 * @extends ServiceEntityRepository<DeviceEntity>
 *
 * @implements IDeviceRepository<DeviceEntity>
 *
 * @method DeviceEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviceEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviceEntity[]    findAll()
 * @method DeviceEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceRepository extends ServiceEntityRepository implements IDeviceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceEntity::class);
    }

    public function save(DeviceEntity $device): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($device);
        $entityManager->flush();
    }

    public function delete(DeviceEntity $device): void
    {
        $entityManager = $this->getEntityManager();
        $device->setActive(false);
        $entityManager->persist($device);
        $entityManager->flush();
    }

    public function findByUuid(string $uuid): ?DeviceEntity
    {
        return $this->findOneBy(['uuid' => $uuid, 'active'=>true]);
    }
    public function findById(string $id): ?DeviceEntity
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findOneByData(Device $data): ?DeviceEntity
    {
        return $this->findOneBy([
                                    'device' => $data->device()->value(),
                                    'ip' => $data->ip()->value(),
                                    'country' => $data->country()->value(),
                                    'location' => $data->location()->value(),
                                    'active'=>true
                                ]);
    }

    public function findOneByIp(string $ip): ?DeviceEntity
    {
        return $this->findOneBy([
                                    'ip' => $ip,
                                    'active'=>true
                                ]);
    }

    public function getAll(): array
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository(DeviceEntity::class)->findBy(["active"=>true]);
    }
}