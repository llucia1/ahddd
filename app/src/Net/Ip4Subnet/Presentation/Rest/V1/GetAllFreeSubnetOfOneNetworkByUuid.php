<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Presentation\Rest\V1;

use Exception;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetAvaibleResponse;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllFreeSubnetsOfOneNetworkByUuid;
use GridCP\Net\Ip4Subnet\Domain\Exception\GetIpsOfOneNetworkByUuidException;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidNetwork;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Lambdish\Phunctional\map;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('api', name: 'api_v1_')]
final class GetAllFreeSubnetOfOneNetworkByUuid extends AbstractController
{
    public function __construct(private readonly GetAllFreeSubnetsOfOneNetworkByUuid $getAllSubnetsNetwork, private readonly LoggerInterface $logger)
    {
    }

    #[Get(
        description: 'Get All Free Subnets of One Network By Uuid with the provided data. - ROL USER',
        summary: 'Get All Free Subnets of One Network By Uuid',
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
                                       "ip" => "192.168.1.1",// NOSONAR
                                       "mask" => 32,// NOSONAR
                                    ],
                                    [
                                      "ip" => "192.168.2.0",// NOSONAR
                                      "mask" => 32,// NOSONAR
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

    #[Parameter(// NOSONAR
        name: 'mask',
        description: 'Free Subnets By Mask Of One Network',
        in: 'query',
        required: true,
        schema: new Schema(
            type: 'integer',
            example: 32
        )

    )]
    #[IsGranted('ROLE_USER')]

    #[Route('/v1/ip4/subnet/network/{uuid}/free', name: 'get_all_subnet_of_one_network_free', methods: ['GET'])]
    public function __invoke(Request $request,string $uuid): JsonResponse
    {
        try {
            
            $this->logger->info('Get All Free Subnets Of One Network By Uuid.');

            $mask = new SubnetMask(filter_var($request->query->get('mask'), FILTER_VALIDATE_INT));
            $networkUuid = new UuidNetwork($uuid);
            
            $allSubnetsAvailable = $this->getAllSubnetsNetwork->__invoke($networkUuid, $mask);

            return $this->json(
                map(
                    fn(Ip4SubnetAvaibleResponse $subnet): array => [
                        'ip'=> $subnet->ip(),
                        'mask'=> $subnet->mask(),
                    ],
                    $allSubnetsAvailable
                ),
                Response::HTTP_OK
            );
        } catch ( Exception $e){
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $getIpsOfOneNetworkByUuidException = new GetIpsOfOneNetworkByUuidException();
            return $getIpsOfOneNetworkByUuidException($e);
        }

    }
}