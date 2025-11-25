<?php
declare(strict_types=1);

namespace GridCP\Device\Infrastructure\DB\MySQL\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use GridCP\Device\Domain\Model\DeviceModel;
use GridCP\Device\Infrastructure\DB\MySQL\Repository\DeviceRepository;
use Ramsey\Uuid\Uuid as Ramsey;

#[ORM\Entity(repositoryClass: DeviceRepository ::class)]
#[ORM\Table(name:'device')]
class DeviceEntity extends  DeviceModel
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36, unique: true)]
    public ?string $uuid;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    public ?string $ip;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    public ?string $device;

    #[ORM\Column(type: 'string', length: 3, nullable: true)]
    public ?string $country;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    public ?string $location;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;


    public function __construct()
    {
        $this->uuid = Ramsey::uuid4()->toString();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid( ?string $uuid): ?static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): static
    {
        $this->device = $device;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}