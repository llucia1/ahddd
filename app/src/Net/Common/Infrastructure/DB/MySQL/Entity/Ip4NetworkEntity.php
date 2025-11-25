<?php
declare(strict_types=1);

namespace GridCP\Net\Common\Infrastructure\DB\MySQL\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use GridCP\Net\Ip4Network\Domain\Model\Ip4NetworkModel;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkRepository;

use GridCP\Common\Infrastructure\MySQL\Helper\ToArrayTrait;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: Ip4NetworkRepository::class)]
#[ORM\Table(name: 'ip4_network')]
class Ip4NetworkEntity extends Ip4NetworkModel// NOSONAR
{
    use ToArrayTrait;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    public ?int $id = null;


    #[ORM\Column(type: 'string',unique: true)]
    public  ?string $uuid;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    public ?string $name_server_1 = null;// NOSONAR

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    public ?string $name_server_2 = null;// NOSONAR

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    public ?string $name_server_3 = null;// NOSONAR

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    public ?string $name_server_4 = null;// NOSONAR

    #[ORM\Column(type: 'integer')]
    public int $priority = 0;

    #[ORM\Column (type: 'boolean')]
    public bool $selectable_by_client = false;// NOSONAR

    #[ORM\Column (type: 'integer')]
    public int $free = 0;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    public ?string $netmask;

    #[ORM\Column(type: 'string', length: 15, nullable: false)]
    public ?string $gateway;

    #[ORM\Column(type: 'string', length: 15, nullable: false)]
    public ?string $broadcast;

    #[ORM\Column (type: 'boolean')]
    public bool $no_arp = true;// NOSONAR

    #[ORM\Column (type: 'boolean')]
    public bool $rir = false;

    #[ORM\Column (type: 'boolean')]
    public bool $active = true;


    #[ORM\OneToMany(mappedBy: "network", targetEntity: "Ip4Entity", fetch: "LAZY")]
    private Collection $ips;
    #[ORM\OneToMany(mappedBy: 'network', targetEntity: Ip4NetworkFloatGroupEntity::class, cascade: ['persist', 'remove'])]
    private Collection $networkFloatGroups;

    public function __construct()
    {
        $this->ips = new ArrayCollection();
        $this->networkFloatGroups = new ArrayCollection();
    }


    public function setId(?int $id):static
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

    public function setUuid(?string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNameServer1(): ?string
    {
        return $this->name_server_1;
    }

    public function setNameServer1(?string $name_server_1): static
    {
        $this->name_server_1 = $name_server_1;

        return $this;
    }

    public function getNameServer2(): ?string
    {
        return $this->name_server_2;
    }

    public function setNameServer2(?string $name_server_2): static
    {
        $this->name_server_2 = $name_server_2;

        return $this;
    }

    public function getNameServer3(): ?string
    {
        return $this->name_server_3;
    }

    public function setNameServer3(?string $name_server_3): static
    {
        $this->name_server_3 = $name_server_3;

        return $this;
    }

    public function getNameServer4(): ?string
    {
        return $this->name_server_4;
    }

    public function setNameServer4(?string $name_server_4): static
    {
        $this->name_server_4 = $name_server_4;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }


    public function setPriority(?int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function isSelectableByClient(): ?bool
    {
        return $this->selectable_by_client;
    }

    public function setSelectableByClient(bool $selectable_by_client): static
    {
        $this->selectable_by_client = $selectable_by_client;

        return $this;
    }

    public function getFree(): ?int
    {
        return $this->free;
    }

    public function setFree(?int $free): static
    {
        $this->free = $free;

        return $this;
    }

    public function getNetmask(): ?string
    {
        return $this->netmask;
    }

    public function setNetmask(?string $netmask): static
    {
        $this->netmask = $netmask;

        return $this;
    }

    public function getGateway(): ?string
    {
        return $this->gateway;
    }

    public function setGateway(?string $gateway): static
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function getBroadcast(): ?string
    {
        return $this->broadcast;
    }

    public function setBroadcast(?string $broadcast): static
    {
        $this->broadcast = $broadcast;

        return $this;
    }

    public function isNoArp(): ?bool
    {
        return $this->no_arp;
    }

    public function setNoArp(bool $no_arp): static
    {
        $this->no_arp = $no_arp;

        return $this;
    }

    public function isRir(): ?bool
    {
        return $this->rir;
    }

    public function setRir(bool $rir): static
    {
        $this->rir = $rir;

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

    
    public function getIps(): Collection
    {
        return $this->ips;
    }

    public function getNetworkFloatGroups(): Collection
    {
        return $this->networkFloatGroups;
    }

    public function setNetworkFloatGroups(Collection $node): self
    {
        $this->networkFloatGroups = $node;
        return $this;
    }
}