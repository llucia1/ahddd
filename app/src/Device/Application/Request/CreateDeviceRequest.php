<?php
declare(strict_types=1);

namespace GridCP\Device\Application\Request;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Schema(
    schema: "CreateDeviceRequest",
    title: "Create Node Request",
    description: "Request schema for creating a new node.",
    required: ["ip", "device", "country", "location"],
    type: "object",
)]
class CreateDeviceRequest extends BaseRequest
{
    #[Property(
        property: "ip",
        description: "The IP address of Device.",
        type: "string",
        format: "ipv4",
        example: "192.168.1.2"
    )]
    #[NotBlank(message: 'IP should not be blank')]
    protected string $ip;

    #[Property(
        property: "device",
        description: "The device",
        type: "string",
        example: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
    )]
    #[NotBlank(message: 'Device should not be blank')]
    protected string $device;
    
    #[Property(
        property: "country",
        description: "The country",
        type: "string",
        example: "ES"
    )]
    #[NotBlank(message: 'Country should not be blank')]
    protected string $country="";

    #[Property(
        property: "location",
        description: "The location",
        type: "string",
        example: "MAD"
    )]
    #[NotBlank(message: 'Location should not be blank')]
    protected string $location="";

    /**
     * @return mixed
     */
    public function ip(): string
    {
        return $this->ip;
    }

    /**
     * @return mixed
     */
    public function device(): string
    {
        return $this->device;
    }

    /**
     * @return mixed
     */
    public function country(): string
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function location(): string
    {
        return $this->location;
    }
}