<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Domain\Model;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkFloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Domain\Model\Ip4FloatGroupModel;

class Ip4NetworkModel
{
    public ?int $id = null;
    public ?string $uuid = null;
    public ?string $name = null;
    public ?string $name_server_1 = null;
    public ?string $name_server_2 = null;
    public ?string $name_server_3 = null;
    public ?string $name_server_4 = null;
    public int $priority = 0;
    public bool $selectableByClient = false;
    public int $free = 0;
    public ?string $netmask = null;
    public ?string $gateway = null;
    public ?string $broadcast = null;

    public ?Ip4FloatGroupModel $floatGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
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

    public function setNameServer1(?string $nameServer1): static
    {
        $this->name_server_1=$nameServer1;
        return $this;
    }

    public function getNameServer2(): ?string
    {
        return $this->name_server_2;
    }

    public function setNameServer2(?string $nameServer2): static
    {
        $this->name_server_2 = $nameServer2;
        return $this;
    }

    public function getNameServer3(): ?string
    {
        return $this->name_server_3;
    }

    public function setNameServer3(?string $nameServer3): static
    {
        $this->name_server_3 = $nameServer3;
        return $this;
    }

    public function getNameServer4(): ?string
    {
        return $this->name_server_4;
    }

    public function setNameServer4(?string $nameServer4): static
    {
        $this->name_server_4 = $nameServer4;
        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): static
    {
        $this->priority=$priority;
        return $this;
    }

    public function isSelectableByClient(): ?bool
    {
        return $this->selectableByClient;
    }

    public function setIsSelectableByClient(?bool $selectableByClient): static
    {
        $this->selectableByClient=$selectableByClient;
        return $this;
    }

    public function getFree(): ?int
    {
        return $this->free;
    }

    public function setFree(?int $free):static
    {
        $this->free = $free;
        return $this;
    }


    public function getNetmask(): ?string
    {
        return $this->netmask;
    }

    public function setNetmask(?string $netmask):static
    {
        $this->netmask=$netmask;
        return $this;
    }

    public function getGateway(): ?string
    {
        return $this->gateway;
    }

    public function setGateway(?string $gateway):static
    {
        $this->gateway=$gateway;
        return $this;
    }


    public function getBroadcast(): ?string
    {
        return $this->broadcast;
    }

    public function setBroadcast(?string $broadcast): static
    {
        $this->broadcast=$broadcast;
        return $this;
    }

    public function setFloatGroup(?Ip4FloatGroupModel $floatGroup):static
    {
        $this->floatGroup = $floatGroup;
        return $this;
    }

    public function getFloatGroup(): ?Ip4FloatGroupModel
    {
        return $this->floatGroup;
    }

}