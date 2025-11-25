<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Validator\Constraints as Assert;

#[Schema(
    schema: "CreateIP4Request",
    title: "Create IP4 Request",
    description: "Request schema for creating a new IP4 record.",
    required: ["ip", "networkUuid"],
    type: "object"
)]
class CreateIP4Request extends BaseRequest
{

    #[Property(
        property: "ip",
        description: "The IP address in IPv4 format.",
        format: "ipv4",
        example: "192.168.1.1"// NOSONAR
    )]

    #[NotBlank(message:"Ip should not be blank")]
    protected string $ip;

    #[Property(
        property: "uuid_network",
        description: "The network Uuid to which this IP address belongs.",
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e"
    )]

    #[NotBlank(message:"Network ID should not be blank")]
    protected string $uuid_network;

    #[Property(
        property: "priority",
        description: "Priority value for the IP address (0-10).",
        example: 8,
        type: "integer",
        minimum: 0,
        maximum: 10
    )]
    #[Assert\Range(min: 0, max: 10, notInRangeMessage: "Priority must be between {{ min }} and {{ max }}.")]
    protected int $priority = 0;

    #[Property(
        property: "tag",
        description: "The Tag IP.",
        format: "string",
        example: "inProgress"// NOSONAR
    )]
    protected ?string $tag = null;

    public function getIP(): string
    {
        return $this->ip;
    }


    public function getUuidNetwork(): string
    {
        return $this->uuid_network;
    }


    public function getPriority(): int
    {
        return $this->priority;
    }


    public function getTag(): ?string
    {
        return $this->tag;
    }


}