<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use GridCP\Client\Infrastructure\DB\MySQL\Entity\ClientEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetOwnerRepository;
use GridCP\Security\Common\Infrastructure\DB\MySQL\Entity\AuthEntity;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserEntity;


#[ORM\Entity(repositoryClass: Ip4SubnetOwnerRepository::class)]
#[ORM\Table(name: "ip4_subnet_owner")]
class Ip4SubnetOwnerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ["unsigned" => true])]
    private $id;

    #[ORM\Column(type: "string", length: 36)]
    private ?string $uuid;

    #[ORM\ManyToOne(targetEntity: Ip4SubnetEntity::class, inversedBy: 'propertySubnet')]
    #[ORM\JoinColumn(name: 'subnet_id', referencedColumnName: 'id', nullable: false)]
    private ?Ip4SubnetEntity $subnet = null;
    
    #[ORM\Column(type: "string", length: 36,)]
    private ?string $clientUuid = null;
    
    #[ORM\Column(type: 'boolean', options: ["default" => true])]
    private $active = true;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $updatedAt = null;


    public function getId(): int
    {
        return $this->id;
    }
    
    public function getUuid(): ?string
    {
        return $this->uuid;
    }
    
    public function getClientUuid(): ?string
    {
        return $this->clientUuid;
    }
    
    public function isActive(): bool
    {
        return $this->active;
    }
    
    public function getSubnet(): ?Ip4SubnetEntity
    {
        return $this->subnet;
    }

    public function setSubnet(?Ip4SubnetEntity $subnet): self
    {
        $this->subnet = $subnet;
        return $this;
    }

    public function setClientUuid(?string $clientUuid): self
    {
        $this->clientUuid = $clientUuid;
        return $this;
    }
    
    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
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
