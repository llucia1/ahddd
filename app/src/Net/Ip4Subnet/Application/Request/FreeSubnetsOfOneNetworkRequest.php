<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Request;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Schema(
    schema: "FreeSubnetsOfOneNetworkRequest",
    title: "Free Subnets By Mask Of One Network Request",
    description: "Request schema for Free Subnets By Mask Of One Network Request.",
    required: ["mask"],
    type: "object",
)]
class FreeSubnetsOfOneNetworkRequest extends BaseRequest
{
    #[Property(
        property: "mask",
        description: "The subnet mask.",
        type: "integer",
        example: 32
    )]

    #[NotBlank(message: 'Mask should not be blank')]
    #[GreaterThan(value: 32, message: 'Mask should be greater than 32')]
    protected int $mask;

    public function getMask(): int
    {
        return $this->mask;
    }
}