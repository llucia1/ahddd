<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Request;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Schema(
    schema: "CreateIpSubnetRequest",
    title: "Create IP Subnet Request",
    description: "Request schema for creating a new IP subnet.",
    required: ["ip", "mask", "id_user", "id_network", "id_float_group"],
    type: "object",
)]
class CreateIpSubnetRequest extends BaseRequest
{
    #[Property(
        property: "ip",
        description: "The IP address of the subnet in IPv4 format.",
        type: "string",
        format: "ipv4",
        example: "192.168.0.1"
    )]

    #[Ip(version: '4', message: 'Invalid IP format')]
    protected ?string $ip = null;

    #[Property(
        property: "mask",
        description: "The subnet mask.",
        type: "integer",
        example: 32
    )]

    #[NotBlank(message: 'Mask should not be blank')]
    #[GreaterThan(value: 0, message: 'Mask should be greater than 0')]
    #[LessThanOrEqual(value: 32, message: 'Mask should be less than or equal to 32')]
    protected int $mask;

    #[Property(
        property: "uuidFloatgroup",
        description: "The floatgroup uuid associated with the subnet.",
        type: "string",
        example: "af08c9c0-5b1e-4e1a-8b1a-0e2e8c0f0028"
    )]

    #[NotBlank(message: 'Floatgroup uuid should not be blank')]
    protected string $uuidFloatgroup;


    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getMask(): int
    {
        return $this->mask;
    }

    public function getUuidFloatgroup(): ?string
    {
        return $this->uuidFloatgroup;
    }
}