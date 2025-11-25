<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Request;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

#[Schema(
    schema: "PatchIpSubnetRequest",
    title: "Edit IP Subnet Request",
    description: "Request schema for creating a new IP subnet.",
    required: [],
    type: "object",
)]
class PatchIpSubnetRequest extends BaseRequest
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

    #[GreaterThan(value: 0, message: 'Mask should be greater than 0')]
    #[LessThanOrEqual(value: 32, message: 'Mask should be less than or equal to 32')]
    protected ?int $mask = null;

    #[Property(
        property: "uuidFloatgroup",
        description: "The floatgroup uuid associated with the subnet.",
        type: "string",
        example: "af08c9c0-5b1e-4e1a-8b1a-0e2e8c0f0028"
    )]

    protected ?string $uuidFloatgroup = null;


    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getMask(): ?int
    {
        return $this->mask;
    }

    public function getUuidFloatgroup(): ?string
    {
        return $this->uuidFloatgroup;
    }
}