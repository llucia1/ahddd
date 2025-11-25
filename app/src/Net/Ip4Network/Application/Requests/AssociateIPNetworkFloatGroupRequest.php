<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Application\Requests;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints\NotBlank;


class AssociateIPNetworkFloatGroupRequest extends BaseRequest
{

    #[Property(
        property: "uuid",
        description: "The uuid of the Float Group.",
        type: "string",
        example: "34k34-df5334-565fdfdf-454534121df"
    )]

    #[NotBlank(message: 'Uuid should not be blank')]
    protected string $uuid;

    public function getuuid(): string
    {
        return $this->uuid;
    }
}