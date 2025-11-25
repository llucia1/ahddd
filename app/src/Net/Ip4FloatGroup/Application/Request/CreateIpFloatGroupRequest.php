<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Application\Request;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Schema(
    schema: "CreateIpFloatGroupRequest",
    title: "Create IP Float Group Request",
    description: "Request schema for creating a new IP float group.",
    required: ["name"],
    type: "object",
)] 
class CreateIpFloatGroupRequest extends BaseRequest
{
    #[Property(
        property: "name",
        description: "The name of the IP float group.",
        type: "string",
        example: "float-group-1"
    )]

    #[NotBlank(message: 'Name should not be blank')]
    protected string $name;

    public function getName(): string
    {
        return $this->name;
    }
}