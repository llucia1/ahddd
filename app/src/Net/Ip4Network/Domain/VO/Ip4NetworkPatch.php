<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Domain\VO;

use GridCP\Common\Domain\Aggregate\AggregateRoot;

class Ip4NetworkPatch extends AggregateRoot
{
    public function __construct(
        private readonly ?Ip4NetworkUUID               $uuid = null,
        private readonly ?Ip4NetworkName               $name = null,
        private readonly ?Ip4NetworkNameServer         $nameServer1 = null,
        private readonly ?Ip4NetworkNameServer         $nameServer2 = null,
        private readonly ?Ip4NetworkNameServer         $nameServer3 = null,
        private readonly ?Ip4NetworkNameServer         $nameServer4 = null,
        private readonly ?Ip4NetworkPriority           $priority = null,
        private readonly ?Ip4NetworkSelectableByClient $selectableByClient = null,
        private readonly ?Ip4NetworkFree               $free = null,
        private readonly ?Ip4NetworkNetMask            $netmask = null,
        private readonly ?Ip4NetworkGateway            $gateway = null,
        private readonly ?Ip4NetworkBroadcast          $broadcast = null,
        private readonly ?Ip4NetworkNoArp              $noArp = null,
        private readonly ?Ip4NetworkRir                $rir = null,
        private readonly ?Ip4NetworkActive             $active = null
        )
    {
    }

    public function create(?Ip4NetworkUUID       $uuid, ?Ip4NetworkName $name, ?Ip4NetworkNameServer $nameServer1,
                           ?Ip4NetworkNameServer $nameServer2, ?Ip4NetworkNameServer $nameServer3,
                           ?Ip4NetworkNameServer $nameServer4, ?Ip4NetworkPriority $priority,
                           ?Ip4NetworkSelectableByClient $selectableByClient,
                           ?Ip4NetworkFree       $free, ?Ip4NetworkNetMask $netmask, ?Ip4NetworkGateway $gateway,
                           ?Ip4NetworkBroadcast  $broadcast, ?Ip4NetworkNoArp $noArp, ?Ip4NetworkRir $rir,
                           ?Ip4NetworkActive     $active): self
    {
        return new self($uuid, $name, $nameServer1, $nameServer2, $nameServer3, $nameServer4, $priority, $selectableByClient, $free, $netmask, $gateway, $broadcast, $noArp, $rir, $active);
    }

    public function Uuid(): ?Ip4NetworkUUID
    {
        return $this->uuid;
    }

    public function Name(): ?Ip4NetworkName
    {
        return $this->name;
    }

    public function NameServer1(): ?Ip4NetworkNameServer
    {
        return $this->nameServer1;
    }

    public function NameServer2(): ?Ip4NetworkNameServer
    {
        return $this->nameServer2;
    }

    public function NameServer3(): ?Ip4NetworkNameServer
    {
        return $this->nameServer3;
    }

    public function NameServer4(): ?Ip4NetworkNameServer
    {
        return $this->nameServer4;
    }

    public function Priority(): ?Ip4NetworkPriority
    {
        return $this->priority;
    }


    public function SelectableByClient(): ?Ip4NetworkSelectableByClient
    {
        return $this->selectableByClient;
    }

    public function Free(): ?Ip4NetworkFree
    {
        return $this->free;
    }

    public function Netmask(): ?Ip4NetworkNetMask
    {
        return $this->netmask;
    }

    public function Gateway(): ?Ip4NetworkGateway
    {
        return $this->gateway;
    }

    public function Broadcast(): ?Ip4NetworkBroadcast
    {
        return $this->broadcast;
    }

    public function NoArp(): ?Ip4NetworkNoArp
    {
        return $this->noArp;
    }

    public function Rir(): ?Ip4NetworkRir
    {
        return $this->rir;
    }

    public function Active(): ?Ip4NetworkActive
    {
        return $this->active;
    }
}