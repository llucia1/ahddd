<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Application\Request;


use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;


class PatchFloatGroupRequest extends BaseRequest
{
    #[Property(
        property: "name",
        description: "The name of the float group.",
        type: "string",
        example: "Madrid"
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9-_]+$/',
        message: 'Name not valid'
    )]
    #[NotBlank(message: 'Name should not be blank')]
    protected string $name;

    public function name(): string
    {
        return $this->name;
    }
}