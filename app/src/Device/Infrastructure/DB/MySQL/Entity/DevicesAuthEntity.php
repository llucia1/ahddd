<?php
declare(strict_types=1);

namespace GridCP\Device\Infrastructure\DB\MySQL\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use GridCP\Device\Infrastructure\DB\MySQL\Repository\DeviceAuthRepository;

#[ORM\Entity(repositoryClass: DeviceAuthRepository::class)]
#[ORM\Table(name: 'devices_auth')]
class DevicesAuthEntity
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private $id;

    #[ORM\Column(type: 'bigint')]
    private $deviceId;

    #[ORM\Column(type: 'integer')]
    private $authId;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private $active = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeviceId(): ?int
    {
        return $this->deviceId;
    }

    public function setDeviceId(int $deviceId): self
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    public function getAuthId(): ?int
    {
        return $this->authId;
    }

    public function setAuthId(int $authId): self
    {
        $this->authId = $authId;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}