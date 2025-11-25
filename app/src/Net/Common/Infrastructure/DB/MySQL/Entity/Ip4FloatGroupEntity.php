<?php

namespace GridCP\Net\Common\Infrastructure\DB\MySQL\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use GridCP\Net\Ip4FloatGroup\Domain\Model\Ip4FloatGroupModel;
use GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Repository\IpFloatGroupsRepository;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\Collections\Collection;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeEntity;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeFloatGroupEntity;

#[ORM\Entity(repositoryClass: IpFloatGroupsRepository::class)]
#[ORM\Table(name: 'ip4_float_group')]
class Ip4FloatGroupEntity extends Ip4FloatGroupModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 36)]
    public ?string $uuid = null;

    #[ORM\Column(length: 15)]
    public ?string $name = null;

    #[ORM\Column]
    public ?bool $active = true;

    #[ORM\OneToMany(mappedBy: 'floatGroup', targetEntity: NodeFloatGroupEntity::class, cascade: ['persist', 'remove'])]
//    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private Collection $nodeFloatGroups;



    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $updatedAt = null;


    #[ORM\OneToMany(mappedBy: 'floatGroup', targetEntity: Ip4NetworkFloatGroupEntity::class, cascade: ['persist', 'remove'])]
    private Collection $networkFloatGroups;
 
    public function __construct()
    {
        $this->nodeFloatGroups = new ArrayCollection();
        $this->networkFloatGroups = new ArrayCollection();
        
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
    
    public function getNetworks(): Collection
    {
        return $this->networkFloatGroups;
    }
    public function setNetworks(Collection $networks): self
    {
        $this->networkFloatGroups = $networks;
        return $this;
    }

    public function setNodeFloatGroups(Collection $node): self
    {
        $this->nodeFloatGroups = $node;
        return $this;
    }

    public function getNodeFloatGroups(): Collection
    {
        return $this->nodeFloatGroups;
    }
    public function getNodes(): Collection
    {
        return $this->nodeFloatGroups->map(fn(NodeFloatGroupEntity $nfg) => $nfg->getNode());
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
