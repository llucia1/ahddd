<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Presentation\Rest\V1;

use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Service\ListIp4Service;
use GridCP\Net\Ip4\Domain\Exceptions\GetIP4Exception;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use InvalidArgumentException;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Parameter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes\Response as OAResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Lambdish\Phunctional\map;

#[Route('/api', name: 'api_v1_')]
class GetIp4 extends AbstractController
{
    public function __construct(private readonly ListIp4Service $listIp4, private readonly LoggerInterface $logger)
    {
    }

    #[Get(
        description: "Get all IP4.",
        summary: "Get all IP4",
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
                                        "uuid" => "a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6",
                                        "ip" => "192.168.1.27",
                                        "priority" => 8,
                                        "network" => [
                                                         "uuid"=> '88b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o588',
                                                         'name'=> '192.168.0.0/30'
                                                     ],
                                        "tag" => [
                                                         "uuid"=> '88b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o588',
                                                         'name'=> 'inProgress'
                                                     ],
                                    ],
                                    [
                                        "uuid" => "0028c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6",
                                        "ip" => "192.168.1.28",
                                        "priority" => 0,
                                        "network" => null,
                                    ]
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
        name: 'GridCPClient',
        description: "The client UUID for the Owner Subnet. If Client_uuid is null, ownership defaults to the organization's account .",
        in: 'header',
        required: false,
        schema: new Schema(
            type: 'string',
            example: '505bb1a7-dcd5-42bc-bda6-a6dde41e89d8'
        ),
    )]
    
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/v1/ip4', name: 'get_all_ip4', methods: ['GET'])]
    public function __invoke(): JsonResponse{
        try {
            $this->logger->info("Get all IP4");
            $ip4 = $this->listIp4->__invoke();

            return new JsonResponse(
                map(
                    fn(Ip4Response $ip4Response): array => [
                        "uuid" => $ip4Response->uuid(),
                        "ip" => $ip4Response->ip(),
                        "priority" => $ip4Response->priority(),
                        'network' => ($ip4Response->network()) ? [
                                                                    'uuid'=>  $ip4Response->network()->uuid(),
                                                                    'name'=> $ip4Response->network()->name()
                                                                 ]: null,
                        "tag" => ( $ip4Response->tag() ? [
                                                            "uuid" => $ip4Response->tag()->uuid(),
                                                            "tag" => $ip4Response->tag()->tag()

                                                        ] : null )
                    ],
                    $ip4->ip4s()
                ),
                Response::HTTP_OK,
                ['Access-Control-Allow-Origin' => '*']
            );
        }catch (ListIp4EmptyException $e){
            $this->logger->error($e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (HttpException $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

}