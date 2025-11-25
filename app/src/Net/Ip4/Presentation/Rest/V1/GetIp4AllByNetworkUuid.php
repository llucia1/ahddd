<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Presentation\Rest\V1;

use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Service\ListIp4ByNetworkUuid;
use GridCP\Net\Ip4\Domain\Exceptions\GetIP4Exception;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;
use GridCP\Net\Ip4\Domain\Service\IListIp4ByNetworkUuid;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;
use InvalidArgumentException;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Parameter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes\Response as OAResponse;
use function Lambdish\Phunctional\map;

#[Route('/api', name: 'api_v1_')]
class GetIp4AllByNetworkUuid extends AbstractController
{
    public function __construct(private readonly ListIp4ByNetworkUuid $listIp4, private readonly LoggerInterface $logger)
    {
    }

    #[Get(
        description: "Get all IP4 By IP4Network Uuid.",
        summary: "Get all IP4 By IP4Network Uuid",
        security: [["Bearer" => []]],
        tags: ["IP4"],
        responses: [
            "200" => new OAResponse(
                response: "200",
                description: "Success",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                                    [
                                        "uuid" => "72b33948-45bd-4898-9465-ca083dbc2d0d",
                                        "ip" => "192.168.1.18",
                                        "priority" => 8
                                    ],
                                    [
                                        "uuid" => "8bdf6e53-9cad-49ac-bb49-56cba10d4c22",
                                        "ip" => "192.168.2.0",
                                        "priority" => 8
                                    ]
                                ],
                            ),
                        ),
            ),
            "204" => new OAResponse(
                response: "204",
                description: "Not Content",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "There are no IP4s",
                        ],
                    ),
                ),
            ),
            "400" => new OAResponse(
                response: "400",
                description: "Bad Request",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "IP4 not Valid",
                        ],
                    ),
                ),
            ),
            
            "404" => new OAResponse(
                response: "404",
                description: "Not Content",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "TNot Found",
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
                            "error" => "An error occurred while fetching IP4s",
                        ],
                    ),
                ),
            ),
        ],
    )]

    #[Parameter(
        name: "uuid",
        description: "Uuid network to obtain all related ips.",
        in: "path",
        required: true,
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e",
    )]    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4/ip4network/{uuid}', name: 'get_all_ip4_by_network_uuid', methods: ['GET'])]
    public function __invoke(string $uuid): JsonResponse{
        try {
            $this->logger->info("Get all IP4 By Netwok Uuid");
            $ip4IdNetwork = new Ip4UuidNetwork($uuid);

            $ip4 = $this->listIp4->__invoke($ip4IdNetwork);

            return new JsonResponse(
                map(
                    fn(Ip4Response $ip4Response): array => [
                        "uuid" => $ip4Response->uuid(),
                        "ip" => $ip4Response->ip(),
                        "priority" => $ip4Response->priority()
                    ],
                    $ip4->ip4s()
                ),
                Response::HTTP_OK,
                ['Access-Control-Allow-Origin' => '*']
            );
        }catch (ListIp4EmptyException $e){
            $this->logger->error($e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NO_CONTENT);
        } catch (InvalidArgumentException $e){
            $this->logger->error('Uuid Not Valid:( -> ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (NerworkNoExistException $e){
            $this->logger->error($e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

}