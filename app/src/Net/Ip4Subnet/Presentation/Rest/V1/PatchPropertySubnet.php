<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Presentation\Rest\V1;

use Exception;
use GridCP\Net\Ip4Subnet\Application\Request\AddPropertySubnetRequest;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Common\Service\PacthPropertySubnetService;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsException;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Schema;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('api', name: 'api_v1_')]
final class PatchPropertySubnet extends AbstractController
{
    public function __construct(private readonly PacthPropertySubnetService $patchPropertySubnet, private readonly LoggerInterface $logger)
    {
    }

    #[Patch(
        description: 'Edit a Owner Subnet with the provided data. - ROL ADMIN',
        summary: 'Edit a Owner Subnet',
        security: [['Bearer' => []]],
        tags: ['IP4 Subnet'],
        responses: [
            "204" => new OAResponse(
                response: "204",
                description: "Added",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ['status' => 'Owner Subnet edited. New user with uuid: d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e'],
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
    #[RequestBody(// NOSONAR
        description: 'Provide the IP4 subnet data to create a new IP4 subnet.',
        required: true,
        content: new MediaType(
            mediaType: 'application/json',
            schema: new Schema(
                ref: new Model(type: AddPropertySubnetRequest::class)
            )
        )
    )]

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4/subnet/{uuid}/owner', name: 'pacth_owner_subnet', methods: ['PATCH'])]
    public function __invoke(AddPropertySubnetRequest $request,string $uuid): JsonResponse
    {
        try {
            $this->logger->info('Start Pacth Owner Subnet: ' . $uuid);
            
            $subnetUUid = new SubnetUuid($uuid);// NOSONAR
            $subnetUuidClient = $request->getUuidClient() ? new UuidClient($request->getUuidClient()) : null;

            $this->patchPropertySubnet->__invoke($subnetUUid, $subnetUuidClient);

            return $this->json(['status' => 'Owner Subnet edited. New user with uuid: ' . $uuid ], Response::HTTP_NO_CONTENT);

            
        } catch ( Exception $e){
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $propertySubnetException= new SubnetsException();
            return $propertySubnetException($e);
        }
    }
}