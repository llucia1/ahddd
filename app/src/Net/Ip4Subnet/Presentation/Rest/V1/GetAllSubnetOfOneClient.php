<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Presentation\Rest\V1;

use Exception;
use GridCP\Net\Ip4Subnet\Application\Help\SubnetsTrait;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnetsOfOneClientByUuid;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsException;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('api', name: 'api_v1_')]
final class GetAllSubnetOfOneClient extends AbstractController
{
    use SubnetsTrait;
    public function __construct(private readonly GetAllSubnetsOfOneClientByUuid $getAllSubnetClients, private readonly LoggerInterface $logger)
    {
    }

    #[Get(
        description: 'Get All Subnet of One Client By Uuid with the provided data. - ROL ADMIN',
        summary: 'Get All Subnet of One Client By Uuid',
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
                                "uuid" => "efe97dd8-676d-4df8-a483-0666a6caf91d",
                                "ip" => "192.168.7.0",// NOSONAR
                                "mask" => 32,
                                "floatgroup" => [
                                    "uuid" => "4f79ad5e-8922-4fd7-a09b-e1eb5ec5b215",
                                    "name" => "Ubrique"
                                 ],
                                "owner" => null
                            ],
                            [
                                "uuid" => "efa302a8-7863-4219-b5bb-d3ee870a0070",
                                "ip" => "192.168.8.0",// NOSONAR
                                "mask" => 32,
                                "floatgroup" => [
                                    "uuid" => "4f79ad5e-8922-4fd7-a09b-e1eb5ec5b215",
                                    "name" => "Ubrique"
                                 ],
                                "owner" => [
                                                "uuid" => "be7194aa-b7dd-4b94-b6fe-b83328d182df",
                                                "clientUuid" => "e9a3baa1-1ca4-4c0d-8a0b-cb877491a486"
                                           ]
                            ],
                            
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
        description: "The client UUID for the Owner Subnet.  If Client_uuid is null, ownership defaults to the organization's account .",
        in: 'header',
        required: false,
        schema: new Schema(
            type: 'string',
            example: '505bb1a7-dcd5-42bc-bda6-a6dde41e89d8'
        ),
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4/subnet/client', name: 'get_all_subnet_of_one_client', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            
            $this->logger->info('Get All Subnets Of One Clients By Uuid.');
            $subnetUuidClient = ($request->headers->get('GridCPClient'))? new UuidClient($request->headers->get('GridCPClient')) : null;
            $allSubnets = $this->subnetsClientResponses($this->getAllSubnetClients->__invoke($subnetUuidClient));
            return $this->json( $this->subnetsResponse($allSubnets->gets()), Response::HTTP_OK );
        } catch ( Exception $e){
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $propertySubnetException = new SubnetsException();
            return $propertySubnetException($e);

        }

    }
}