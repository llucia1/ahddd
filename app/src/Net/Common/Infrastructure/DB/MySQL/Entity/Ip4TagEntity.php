<?php
declare(strict_types=1);

namespace GridCP\Net\Common\Infrastructure\DB\MySQL\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use GridCP\Net\Ip4Tag\Infrastructure\DB\MySQL\Repository\Ip4TagRepository;
use JMS\Serializer\Annotation as JMS;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Ramsey\Uuid\Uuid as Ramsey;

#[JMS\ExclusionPolicy(policy: 'all')]
#[ORM\Entity(repositoryClass: Ip4TagRepository::class)]
#[ORM\Table(name: 'ip4_tag')]
class Ip4TagEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    public ?int $id = null;
    
    #[ORM\Column(type: "string")]
    public string $uuid;
    
    #[ORM\Column(type: "string", nullable: true)]
    public ?string $tag = null;
    
    #[ORM\ManyToOne(targetEntity: Ip4Entity::class, inversedBy: 'tags')]
    #[ORM\JoinColumn(name: 'id_ip', referencedColumnName: 'id', nullable: false)]
    private ?Ip4Entity $ip = null;

    #[ORM\Column(type: "boolean")]
    public ?bool $active = true;


    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $updatedAt = null;


    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]




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

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setTag(?string $tag): void
    {
        $this->tag = $tag;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getActive(): bool
    {
       return $this->active;
    }



    public function setCreatedAt(?DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    public function getIp(): ?Ip4Entity
    {
        return $this->ip;
    }

    public function setIp(?Ip4Entity $ip): void
    {
        $this->ip = $ip;
    }
}