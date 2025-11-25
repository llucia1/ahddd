<?php
declare(strict_types=1);

namespace GridCP\Net\Common\Infrastructure\DB\MySQL\Entity;

use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;
use GridCP\Common\Infrastructure\MySQL\Helper\ToArrayTrait;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkFloatGroupRepository;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: Ip4NetworkFloatGroupRepository::class)]
#[ORM\Table(name: 'ip4_network_float_gorup')]
class Ip4NetworkFloatGroupEntity
{
    use ToArrayTrait;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $active;

    #[ORM\ManyToOne(targetEntity: Ip4FloatGroupEntity::class, inversedBy: 'networkFloatGroups', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'floatgroup_id', referencedColumnName: 'id', nullable: false)]
    private Ip4FloatGroupEntity $floatGroup;

    #[ORM\ManyToOne(targetEntity: Ip4NetworkEntity::class, inversedBy: 'networkFloatGroups')]
    #[ORM\JoinColumn(name: 'network_id', referencedColumnName: 'id', nullable: false)]
    private ?Ip4NetworkEntity $network = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
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

    public function getFloatGroup(): Ip4FloatGroupEntity
    {
        return $this->floatGroup;
    }

    public function setFloatGroup(Ip4FloatGroupEntity $floatGroup): self
    {
        $this->floatGroup = $floatGroup;
        return $this;
    }

    public function getNetwork(): ?Ip4NetworkEntity
    {
        return $this->network;
    }

    public function setNetwork(?Ip4NetworkEntity $network): self
    {
        $this->network = $network;
        return $this;
    }


}