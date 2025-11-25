<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Presentation\Rest\V1;

use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkResponse;
use GridCP\Net\Ip4Network\Application\Services\ListIpNetwork;
use GridCP\Net\Ip4Network\Domain\Exception\GetIPNetworkException;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Response as OAResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use function Lambdish\Phunctional\map;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
final class GetIpNetwork extends AbstractController
{
    public function __construct(private readonly ListIpNetwork $listIpNetwork, private readonly LoggerInterface $logger)
    {
    }

    #[Get(
        description: "Get all IP4 Networks.",
        summary: "Get all IP4 Networks",
        security: [["Bearer" => []]],
        tags: ["IP4 Network"],
        responses: [
            "200" => new OAResponse(
                response: "200",
                description: "Success",
                content: new MediaType(
                    mediaType: "application/json",
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
            "404" => new OAResponse(
                response: "204",
                description: "Not Content",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "There are no IP Networks",
                        ],
                    ),
                ),
            ),
            "500" => new OAResponse(
                response: "500",
                description: "Internal Server Error",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "An error occurred while fetching IP networks",
                        ],
                    ),
                ),
            ),
        ],
    )]
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4_network', name: 'get_ip4_network', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        try {
            $this->logger->info('Get All IP4 Networks');
            $networks = $this->listIpNetwork->__invoke();
            return new JsonResponse(
                map(
                    fn(Ip4NetworkResponse $ip4Network): array => [
                        'uuid' => $ip4Network->uuid(),
                        'name' => $ip4Network->name(),
                        'name_server1' => $ip4Network->name_server_1(),
                        'name_server2' => $ip4Network->name_server_2(),
                        'name_server3' => $ip4Network->name_server_3(),
                        'name_Server4' => $ip4Network->name_server_4(),
                        'priority' => $ip4Network->priority(),
                        'netMask' => $ip4Network->netMask(),
                        'gateway' => $ip4Network->gateway(),
                        'broadcast' => $ip4Network->broadcast(),
                        'float_group'=> $ip4Network->floatGroup()
                    ],
                    $networks->ip4_networks()
                ),
                Response::HTTP_OK
            );
        }catch (ListIp4NetworkEmptyException $e){
            $this->logger->error($e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (HttpException $e) {
            $this->logger->error($e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
        }

    }
}