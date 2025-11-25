<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Application\Requests;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[Schema(
    schema: "PatchIPNetworkRequest",
    title: "Patch IP Network Request",
    description: "Request schema for edit a edit IP network.",
    required: [],
    type: "object",
)]
class PatchIPNetworkRequest extends BaseRequest
{
    #[Property(
        property: "name",
        description: "The name of the IP network.",
        type: "string",
        example: "name"
    )]
    protected ?string $name=null;

    #[Property(
        property: "name_server1",
        description: "The first name server IP address in IPv4 format.",
        type: "string",
        format: "ipv4",
        example: "192.168.1.1"
    )]

    #[Ip(version: '4', message: 'Invalid IP format')]
    protected ?string $name_server1 = null;

    #[Property(
        property: "name_server2",
        description: "The second name server IP address in IPv4 format.",
        type: "string",
        format: "ipv4",
        example: "192.168.1.2"
    )]

    #[Ip(version: '4', message: 'Invalid IP format')]
    protected ?string $name_server2=null;

    #[Property(
        property: "name_server3",
        description: "The third name server IP address in IPv4 format.",
        type: "string",
        format: "ipv4",
        example: "192.168.1.3"
    )]

    #[Ip(version: '4', message: 'Invalid IP format')]
    protected ?string $name_server3=null;

    #[Property(
        property: "name_server4",
        description: "The fourth name server IP address in IPv4 format.",
        type: "string",
        nullable: true,
        format: "ipv4",
        example: "192.168.1.4"
    )]

    #[Ip(version: '4', message: 'Invalid IP format')]
    protected ?string $name_server4=null;

    #[Property(
        property: "priority",
        description: "The priority of the IP network.",
        type: "integer",
        example: 50
    )]
    #[PositiveOrZero(message: 'Priority should be a positive number')]
    #[LessThanOrEqual(value: 100, message: 'Priority should be less than or equal to 100')]
    protected ?int $priority = null;

    #[Property(
        property: "free",
        description: "The free of the IP network.",
        type: "integer",
        example: 50
    )]
    #[PositiveOrZero(message: 'free should be a positive number')]
    protected ?int $free = null;

    #[Property(
        property: "netmask",
        description: "The netmask in IPv4 format.",
        type: "string",
        format: "ipv4",
        example: "255.255.255.0"
    )]


    #[Ip(version: '4', message: 'Invalid IP format')]
    protected ?string $netmask = null;

    #[Property(
        property: "gateway",
        description: "The gateway IP address in IPv4 format.",
        type: "string",
        format: "ipv4",
        example: "192.168.1.254"
    )]


    #[Ip(version: '4', message: 'Invalid IP format')]
    protected ?string $gateway = null;

    #[Property(
        property: "broadcast",
        description: "The broadcast IP address in IPv4 format.",
        type: "string",
        format: "ipv4",
        example: "192.168.1.255"
    )]

    #[Ip(version: '4', message: 'Invalid IP format')]
    protected ?string $broadcast = null;


    #[Property(
        property: "selectable_by_client",
        description: "The selectable by client in IPv4 Network.",
        type: "string",
        example: true
    )]
    protected ?bool $selectable_by_client = null;
    #[Property(
        property: "no_arp",
        description: "The no arp in IPv4 format.",
        type: "string",
        example: true
    )]
    protected ?bool $no_arp = null;
    #[Property(
        property: "rir",
        description: "The rir in IPv4 format.",
        type: "string",
        example: true
    )]
    protected ?bool $rir = null;
    #[Property(
        property: "active",
        description: "The active in IPv4 format.",
        type: "string",
        example: true
    )]
    protected ?bool $active = null;




    public function getNameServer1(): ?string
    {
        return $this->name_server1 ;
    }

    public function getNameServer2(): ?string
    {
        return $this->name_server2;
    }

    public function getNameServer3(): ?string
    {
        return $this->name_server3;
    }

    public function getNameServer4(): ?string
    {
        return $this->name_server4;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }
    public function getFree(): ?int
    {
        return $this->free;
    }


    public function getNetmask(): ?string
    {
        return $this->netmask;
    }

    public function getGateway(): ?string
    {
        return $this->gateway;
    }

    public function getBroadcast(): ?string
    {
        return $this->broadcast;
    }
    public function getSelectable_by_client(): ?bool
    {
        return $this->selectable_by_client;
    }
    public function getNo_arp(): ?bool
    {
        return $this->no_arp;
    }
    public function getRir(): ?bool
    {
        return $this->rir;
    }
    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}