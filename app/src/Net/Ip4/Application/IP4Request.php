<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application;

use GridCP\Common\Application\BaseRequest;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Ip;



use GridCP\Common\Domain\Utils\CIDR;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;




#[OA\Schema(
    schema: "IP4Request",
    title: "IP4 Request",
    description: "Request schema for IP4.",
    required: ["ips"],
    type: "object"
)]
class IP4Request extends BaseRequest
{
    #[OA\Property(
        property: "ips",
        description: "An array of IP addresses in IPv4 format or CIDR notation.",
        type: "array",
        items: new OA\Items(
            type: "string",
            example: "192.168.1.1"
        ),
        example: ["192.168.1.1", "129.1.1.0/30"]
    )]
    #[Assert\NotBlank(message: "IP addresses should not be blank.")]
    #[Assert\All([
        new Assert\NotBlank(),
        new CIDR(),
    ])]
    protected array $ips;

    public function getIPs(): array
    {
        return $this->ips;
    }
}