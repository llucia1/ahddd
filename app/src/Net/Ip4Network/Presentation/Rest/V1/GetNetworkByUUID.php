<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Presentation\Rest\V1;

use GridCP\Net\Ip4Network\Application\Services\ListIP4NetworkByUUIDService;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
use InvalidArgumentException;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
class GetNetworkByUUID extends AbstractController
{
    public function __construct(
        private readonly ListIP4NetworkByUUIDService $listIP4NetworkByUUIDService,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Get(
        description: "Get an IP4 Network with the given UUID.",
        summary: "Get an IP4 Network",
        security: [["Bearer" => []]],
        tags: ["IP4 Network"],
        responses: [
            "200" => new OAResponse(
                response: "200",
                description: "Success",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: [
                            "uuid" => "a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6",
                            "name" => "192.168.0.1/24",
                            "name_server1" => "192.168.1.1",
                            "name_server2" => "192.168.1.2",
                            "name_server3" => "192.168.1.3",
                            "name_server4" => "192.168.1.4",
                            "priority" => 50,
                            "netmask" => "255.255.255.0",
                            "gateway" => "192.168.1.254",
                            "broadcast" => "192.168.1.255",
                            "created_at" => "2021-01-01T00:00:00+00:00",
                            "updated_at" => "2021-01-01T00:00:00+00:00",
                            "active" => true,
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
                            "error" => "Uuid IP4 not Valid",// NOSONAR
                        ],
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "404",
                description: "Network does not exist",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ["'error': 'Not Found Ip4Networks'"],// NOSONAR
                    ),
                ),
            ),
            "500" => new OAResponse(
                response: "500",
                description: "Internal Server Error",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Internal Server Error"],// NOSONAR
                    ),
                ),
            ),
        ],
    )]

    #[Parameter(
        name: "uuid",
        description: "The UUID of the IP4 Network to delete.",
        in: "path",
        required: true,
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e",
    )]
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4_network/{uuid}', name: 'get_network_by_uuid', methods: ['GET'])]
    public function __invoke(string $uuid): JsonResponse
    {
        try {
            $uuid = new Ip4NetworkUUID($uuid);
            $this->logger->info('Get IP4 Network with UUID: ' . $uuid);
            $network = $this->listIP4NetworkByUUIDService->__invoke($uuid);
            $result = new JsonResponse(
                [
                   'uuid' => $network->uuid(),
                   'name' => $network->name(),
                    'name_server1' => $network->name_server_1(),
                    'name_server2' => $network->name_server_2(),
                    'name_server3' => $network->name_server_3(),
                    'name_Server4' => $network->name_server_4(),
                    'priority' => $network->priority(),
                    'netMask' => $network->netMask(),
                    'gateway' => $network->gateway(),
                    'floatsGroup' => $network->floatGroup(),
                    'broadcast' => $network->broadcast()
                    ],
                Response::HTTP_OK
            );
        }catch (ListIp4NetworkEmptyException $e){
            $this->logger->error($e->getMessage());
            $result = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);// NOSONAR
        } catch (InvalidArgumentException $e){
            $this->logger->error('Uuid Not Valid:( -> ' . $e->getMessage());
            $result = new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (HttpException $e) {
            $result = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());// NOSONAR
        }
        return $result;
    }
}