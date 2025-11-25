<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Presentation\Rest\V1;

use Exception;
use GridCP\Net\Ip4Subnet\Application\Help\SubnetsTrait;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnets;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnetsOfOneClientByUuid;
use GridCP\Net\Ip4Subnet\Application\Service\GetIp4SubnetService;
use GridCP\Net\Ip4Subnet\Common\Service\GetAllSubnetsService;
use GridCP\Net\Ip4Subnet\Domain\Exception\PropertySubnetException;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetException;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsException;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsNoFound;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Lambdish\Phunctional\map;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Parameter;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
#[Route('api', name: 'api_v1_')]
final class GetSubnet extends AbstractController
{
    use SubnetsTrait;

    public function __construct( private readonly GetIp4SubnetService $getSubnetsService, private readonly LoggerInterface $logger)
    {
    }

    #[Get(
        description: 'Get Subnet with the provided data. - ROL USER',
        summary: 'Get Subnet',
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
                                           "uuid" => "efe97dd8-676d-4df8-a483-0666a6caf91d",
                                           "ip" => "192.168.7.0",// NOSONAR
                                           "mask" => 32,
                                           "uuidfloatgroup" => "4f79ad5e-8922-4fd7-a09b-e1eb5ec5b215",
                                           "owner" => [
                                                        "uuid" => "12d917be-f47b-4da3-a2c1-081aac8d778f",
                                                        "uuidClient" =>"e9a3baa1-1ca4-4c0d-8a0b-cb877491a486"
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
    #[Route('/v1/ip4/subnet/{uuid}', name: 'get_one_subnet', methods: ['GET'])]
    public function __invoke(string $uuid): JsonResponse
    {
        try {
           
                $uuidSubnet = new SubnetUuid($uuid);
                $subnet = $this->getSubnetsService->__invoke($uuidSubnet);

            return $this->json( $this->subnetsArrayResponse($subnet->get()), Response::HTTP_OK );
        } catch ( \Exception $e){
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $getIpsOfOneNetworkByUuidException = new SubnetException();
            return $getIpsOfOneNetworkByUuidException($e);
        }
    }
}