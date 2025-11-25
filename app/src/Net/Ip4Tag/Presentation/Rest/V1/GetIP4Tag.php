<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Presentation\Rest\V1;

use Exception;
use GridCP\Net\Ip4Tag\Application\Service\GetIP4TagsService;
use GridCP\Net\Ip4Tag\Domain\Exception\CreateIp4TagException;
use GridCP\Net\Ip4Tag\Domain\Exception\Ip4TagsExceptions;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagUuid;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api', name: 'api_v1_')]
final class GetIP4Tag extends AbstractController
{
    public function __construct(private readonly GetIP4TagsService $getIP4Tag, private readonly LoggerInterface $logger)
    {
    }

    /**
     * @throws CreateIp4TagException
     */
    #[Get(
        description: "Get a IP4 Tag with the provided data.",
        summary: "Get IP4 Tag",
        security: [["Bearer" => []]],
        tags: ["IP4 Tag"],
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
                            "gcp_node_name" => "node1",
                            "pve_node_name" => "node1",
                            "pve_hostname" => "node1",
                            "ip" => [
                                        "vendor" => "GenuineIntel",
                                        "name" => "KnightsMill",
                                        "custom" => 0,
                            ]
                        ],
                    ),
                ),
            ),
            "409" => new OAResponse(
                response: "409",
                description: "Conflict",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Tag already exists."],
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
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/v1/ip4_tag/{uuid}', name: 'get_ip4_tag', methods: ['GET'])]
    public function __invoke(string $uuid): JsonResponse
    {
        try {
            $this->logger->info("Get a IP4 Tag", ["uuid" => $uuid]);
            $tagUUID = new Ip4TagUuid($uuid);
            $ipTag = $this->getIP4Tag->__invoke($tagUUID);


            return new JsonResponse([
                    'uuid' => $ipTag->uuid(),
                    'tag' => $ipTag->tag(),
                    "ip" => [
                        'uuid' => $ipTag->ip4()->getUuid(),
                        "ip" => $ipTag->ip4()->getIp(),
                    ]
                ], Response::HTTP_OK);
        } catch (Exception $e) {
                $this->logger->error('Exception:( -> ' . $e->getMessage());
                $vmException = new Ip4TagsExceptions();
                return $vmException($e);
        }
        
    }
}