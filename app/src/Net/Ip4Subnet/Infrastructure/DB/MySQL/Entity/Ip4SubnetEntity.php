<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmEntity;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: Ip4SubnetRepository::class)]
#[ORM\Table(name: 'ip4_subnet')]
class Ip4SubnetEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ["unsigned" => true])]
    private int $id;

    #[ORM\Column(type: "string", length: 36, nullable: false)]
    private string $uuid;
    
    #[ORM\Column(type: "string", length: 36,)]
    private ?string $uuidFloatgroup = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $ip = null;

    #[ORM\Column]
    private ?int $mask = null;

    #[ORM\Column]
    private ?bool $active = true;

    #[ORM\OneToMany(mappedBy: "subnet", targetEntity: Ip4SubnetOwnerEntity::class, cascade: ["persist", "remove"], fetch: "EAGER")]
    private Collection $propertySubnet;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $updatedAt = null;



    public function __construct()
    {
        $this->propertySubnet = new ArrayCollection();
    }
    

    

    public function getId(): int
    {
        return $this->id;
    }
    
    public function getUuid(): string
    {
        return $this->uuid;
    }
    
    public function getIp(): ?string
    {
        return $this->ip;
    }
    
    public function getMask(): ?int
    {
        return $this->mask;
    }
    
    public function isActive(): ?bool
    {
        return $this->active;
    }
    
    public function getPropertySubnet(): Collection
    {
        return $this->propertySubnet;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }
    public function getUuidFloatgroup(): ?string
    {
        return $this->uuidFloatgroup;
    }

    public function setFloatgroup(?string $uuidFloatgroup): self
    {
        $this->uuidFloatgroup = $uuidFloatgroup;
        return $this;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function setMask(int $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function setActive(bool $active): static
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