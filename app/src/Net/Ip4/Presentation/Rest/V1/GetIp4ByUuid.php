<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Presentation\Rest\V1;

use GridCP\Net\Ip4\Application\Service\ListIp4ByUuidService;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NoFoundException;
use GridCP\Net\Ip4\Domain\VO\Ip4Uuid;
use InvalidArgumentException;
use OpenApi\Attributes\Get;
use Psr\Log\LoggerInterface;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Response as OAResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api', name: 'api_v1_')]
class GetIp4ByUuid extends AbstractController
{
    public function __construct(
        private readonly ListIP4ByUuidService $listIP4ByUUIDService,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Get(
        description: "Get an IP4 with the given UUID.",
        summary: "Get an IP4",
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
                            "uuid" => "a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6",
                            "ip" => "192.168.1.27",
                            "network" => [
                                            "uuid"=> 'uuid',
                                            'ip'=> '192.168.0.0/30',
                                            'priority'=> 8
                            ],
                            "tag" => [
                                                        "uuid"=> '88b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o588',
                                                        'name'=> 'inProgress'
                                                    ],
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
                            "error" => "Uuid IP4 not Valid",
                        ],
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "404",
                description: "IP4 does not exist",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["'error': 'Not Found Ip4s'"],
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
                        example: ["error" => "Internal Server Error"],
                    ),
                ),
            ),
        ],
    )]

    #[Parameter(
        name: "uuid",
        description: "The UUID of the IP4 to get.",
        in: "path",
        required: true,
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e",
    )]    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4/{uuid}', name: 'get_ip4_by_uuid', requirements: ['uuid'=>Requirement::UUID_V4], methods: ['GET'])]
    public function __invoke(string $uuid): JsonResponse
    {
        try {
            $ip4UUID = new Ip4Uuid($uuid);
            $this->logger->info("Get IP4 with UUID: $uuid");

            $ip4 = $this->listIP4ByUUIDService->__invoke($ip4UUID);
            return new JsonResponse(
                [
                    'uuid' => $ip4->uuid(),
                    'ip' => $ip4->ip(),
                    'priority' => $ip4->priority(),
                    'network' => ($ip4->network()) ? [
                                                        'uuid'=>  $ip4->network()->uuid(),
                                                        'name'=> $ip4->network()->name()
                                                    ]: null,
                    "tag" => ( $ip4->tag() ? [
                                                            "uuid" => $ip4->tag()->uuid(),
                                                            "tag" => $ip4->tag()->tag()

                                                        ] : null )
                ],
                Response::HTTP_OK,
                ['Access-Control-Allow-Origin' => '*']
            );
        } catch (InvalidArgumentException $e){
            $this->logger->error('Uuid Not Valid:( -> ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Ip4NoFoundException $e) {
            $this->logger->error($e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}