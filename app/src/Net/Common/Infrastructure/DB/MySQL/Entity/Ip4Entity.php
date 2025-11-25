<?php
declare(strict_types=1);

namespace GridCP\Net\Common\Infrastructure\DB\MySQL\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use GridCP\Net\Ip4\Domain\Model\Ip4;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Ramsey\Uuid\Uuid as Ramsey;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
#[ORM\Entity(repositoryClass: Ip4Repository::class)]
#[ORM\Table(name: 'ip4')]
class Ip4Entity
//class Ip4Entity extends Ip4
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    public ?int $id = null;
    
    #[ORM\Column(type: "string", unique: true)]
    public string $uuid;
    

    #[ORM\Column(type: "string", length: 15, nullable: true)]
    public ?string $ip;

    #[ORM\ManyToOne(targetEntity: "Ip4NetworkEntity", inversedBy: "ips", fetch: "LAZY")]
    #[ORM\JoinColumn(name: "id_network", referencedColumnName: "id", nullable: true)]
    private ?Ip4NetworkEntity $network = null;

    
    #[ORM\Column(type: "boolean")]
    public ?bool $active = true;

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 0, max: 10, notInRangeMessage: "Priority must be between {{ min }} and {{ max }}.")]
    public int $priority = 0;
    
    #[ORM\OneToMany(mappedBy: 'ip', targetEntity: Ip4TagEntity::class, cascade: ['persist'])]
    private Collection $tags;


    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->uuid = Ramsey::uuid4()->toString();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string|null $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): void
    {
        $this->ip = $ip;
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


    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool|null $active): void
    {
        $this->active = $active;
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

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }
    public function getActiveTag(): ?Ip4TagEntity
    {
        foreach ($this->tags as $tag) {
            if ($tag->getActive()) {
                return $tag;
            }
        }

        return null;
    }
    public function addTag(Ip4TagEntity $tag): void
    {
        $this->tags->add($tag);
        $tag->setIp($this);
    }

}
