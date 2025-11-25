<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Presentation\Rest\V1;

use Exception;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnetsOfOneNetworkByUuid;
use GridCP\Net\Ip4Subnet\Domain\Exception\GetIpsOfOneNetworkByUuidException;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidNetwork;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Lambdish\Phunctional\map;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes\Parameter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('api', name: 'api_v1_')]
final class GetAllSubnetOfOneNetworkByUuid extends AbstractController
{
    public function __construct(private readonly GetAllSubnetsOfOneNetworkByUuid $getAllSubnetsNetwork, private readonly LoggerInterface $logger)
    {
    }

    #[Get(
        description: 'Get All Subnet of One Network By Uuid with the provided data. - ROL USER',
        summary: 'Get All Subnet of One Network By Uuid',
        security: [['Bearer' => []]],
        tags: ['IP4 Subnet'],
        responses: [
            "200" => new OAResponse(
                response: "200",
                description: "Ok",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: [
                                    [
                                       "uuid" => "62fe85c3-56de-4a64-84be-1e64c7c3c33d",// NOSONAR
                                       "ip" => "192.168.1.1",// NOSONAR
                                       "mask" => 32,// NOSONAR
                                       "floatgroupUuid" => "4f79ad5e-8922-4fd7-a09b-e1eb5ec5b215"// NOSONAR
                                    ],
                                    [
                                      "uuid" => "505bb1a7-dcd5-42bc-bda6-a6dde41e89d8",// NOSONAR
                                      "ip" => "192.168.2.0",// NOSONAR
                                      "mask" => 32,// NOSONAR
                                      "floatgroupUuid" => "4f79ad5e-8922-4fd7-a09b-e1eb5ec5b215"// NOSONAR
                                    ]
                                ],
                    ),
                ),
            ),
            "400" => new OAResponse(
                response: "400",
                description: "Bad Request",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "Bad Request",// NOSONAR
                        ],
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "404",
                description: "Not Found",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Not Found Subnet"],
                    ),
                ),
            ),
            "409" => new OAResponse(
                response: "409",
                description: "Conflict",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Not Found Input Data"],// NOSONAR
                    ),
                ),
            ),
            '500' => new OAResponse(
                response: '500',
                description: 'Internal Server Error',
                content: new MediaType(
                    mediaType: 'application/json',// NOSONAR
                    schema: new Schema(
                        type: 'object',
                        example: ['error' => 'Internal Server Error'],// NOSONAR
                    ),
                ),
            ),
        ],
        
    )]
    #[Parameter(
        name: 'GridCPClient',
        description: "The client UUID for the Owner Subnet. If Client_uuid is null, ownership defaults to the organization's account .",
        in: 'header',
        required: false,
        schema: new Schema(
            type: 'string',
            example: '505bb1a7-dcd5-42bc-bda6-a6dde41e89d8'
        ),
    )]
    #[IsGranted('ROLE_USER')]

    #[Route('/v1/ip4/subnet/network/{uuid}', name: 'get_all_subnet_of_one_network', methods: ['GET'])]
    public function __invoke(string $uuid): JsonResponse
    {
        try {
            
            $this->logger->info('Get All Subnets Of One Network By Uuid.');

            $networkUuid = new UuidNetwork($uuid);
            
            $allSubnets = $this->getAllSubnetsNetwork->__invoke($networkUuid);

            return $this->json(
                map(
                    fn(Ip4SubnetResponse $subnet): array => [
                        'uuid' => $subnet->uuid(),
                        'ip'=> $subnet->ip(),
                        'mask'=> $subnet->mask()
                    ],
                    $allSubnets->gets()
                ),
                Response::HTTP_OK
            );
        } catch ( Exception $e){
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $getIpsOfOneNetworkByUuidException = new GetIpsOfOneNetworkByUuidException();
            $getIpsOfOneNetworkByUuidException($e);
        }
    }
}