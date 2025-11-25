<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Application\Request;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

#[Schema(
    schema: "CreateIP4TagRequest",
    title: "Create IP4 Tag Request",
    description: "Request schema for creating a new IP4 tag.",
    required: ["uuidIp", "tag"],
    type: "object",
)]
class CreateIP4TagRequest extends BaseRequest
{
    #[Property(
        property: "uuidIp",
        description: "The uuid of the IP4.",
        type: "string",
        example: 1
    )]

    #[Positive(message: 'Id should be positive')]
    protected ?string $uuidIp = null;

    #[Property(
        property: "tag",
        description: "The tag of the IP4.",
        type: "string",
        example: "Reserved"
    )]

    #[NotBlank(message: 'Tag should not be blank')]
    protected ?string $tag = null;

    public function getUuidIp(): ?string
    {
        return $this->uuidIp;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }
}