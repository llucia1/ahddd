<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Request;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: "AddPropertySubnetRequest",
    title: "Add Property Subnet Request",
    description: "Request schema for add a property subnet.",
    required: ['uuidClient'],
    type: "object",
)]
class AddPropertySubnetRequest extends BaseRequest
{
    #[Property(
        property: "uuidClient",
        description: "The user Uuid associated with the subnet. Client_uuid (owner) or if == null GinerNet",
        type: "string",
        example: "e9a3baa1-1ca4-4c0d-8a0b-cb877491a486"
    )]

    protected ?string $uuidClient = null;

    public function getUuidClient(): ?string
    {
        return $this->uuidClient;
    }



    
}