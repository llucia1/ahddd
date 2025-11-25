<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Presentation\Rest\V1;

use Exception;
use GridCP\Net\Ip4Subnet\Application\Service\DeletePropertySubnetService;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsException;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Attributes\Parameter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('api', name: 'api_v1_')]
final class DeletePropertySubnet extends AbstractController
{
    public function __construct(private readonly DeletePropertySubnetService $deletePropertySubnet, private readonly LoggerInterface $logger)
    {
    }

    #[Delete(// NOSONAR
        description: 'Delete a Owner subnet by its uuid. - ROL ADMIN',// NOSONAR
        summary: 'Delete a Owner Subnet',// NOSONAR
        security: [['Bearer' => []]],// NOSONAR
        tags: ['IP4 Subnet'],// NOSONAR
        responses: [// NOSONAR
            "204" => new OAResponse(// NOSONAR
                response: "204",// NOSONAR
                description: "Added",// NOSONAR
                content: new MediaType(// NOSONAR
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(// NOSONAR
                        type: "object",// NOSONAR
                        example: ['The Owner Subnet has been deleted.'],// NOSONAR
                    ),
                ),
            ),
            "400" => new OAResponse(
                response: "400",// NOSONAR
                description: "Bad Request",// NOSONAR
                content: new MediaType(// NOSONAR
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(// NOSONAR
                        type: "object",// NOSONAR
                        example: [// NOSONAR
                            "error" => "Not Found",// NOSONAR
                        ],
                    ),
                ),
            ),
            "409" => new OAResponse(
                response: "409",// NOSONAR
                description: "Conflict",// NOSONAR
                content: new MediaType(// NOSONAR
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(// NOSONAR
                        type: "object",// NOSONAR
                        example: ["error" => "Not Found Input Data"],// NOSONAR
                    ),
                ),
            ),
            '500' => new OAResponse(// NOSONAR
                response: '500',// NOSONAR
                description: 'Internal Server Error',// NOSONAR
                content: new MediaType(// NOSONAR
                    mediaType: 'application/json',// NOSONAR
                    schema: new Schema(// NOSONAR
                        type: 'object',// NOSONAR
                        example: ['error' => 'Internal Server Error'],// NOSONAR
                    ),
                ),
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/v1/ip4/subnet/{uuid}/owner', name: 'delete_owner_subnet', methods: ['DELETE'])]// NOSONAR
    public function __invoke(string $uuid): JsonResponse// NOSONAR
    {
        
        try {
            $this->logger->info('Start Delete Owner Subnet.');// NOSONAR
            $subnetUUid = new SubnetUuid($uuid);// NOSONAR
            $this->deletePropertySubnet->__invoke($subnetUUid);// NOSONAR
            return $this->json(['status' => 'The Owner Subnet has been deleted' ], Response::HTTP_NO_CONTENT);// NOSONAR
            
        } catch ( Exception $e){
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $propertySubnetException = new SubnetsException();
            $result = $propertySubnetException($e);
        }
        return $result;
    }
}