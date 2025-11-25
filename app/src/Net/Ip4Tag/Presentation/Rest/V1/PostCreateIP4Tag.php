<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Presentation\Rest\V1;

use Exception;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Ip4Tag\Application\Request\CreateIP4TagRequest;
use GridCP\Net\Ip4Tag\Application\Service\CreateIP4Tag;
use GridCP\Net\Ip4Tag\Domain\Exception\CreateIp4TagException;
use GridCP\Net\Ip4Tag\Domain\Exception\Ip4TagsExceptions;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4Tag;
use GridCP\Common\Domain\ValueObjects\Ip4TagTag;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagUuid;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagUuidIp;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\RequestBody;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api', name: 'api_v1_')]
final class PostCreateIP4Tag extends AbstractController
{
    public function __construct(private readonly CreateIP4Tag $createIP4Tag, private readonly LoggerInterface $logger)
    {
    }

    /**
     * @throws CreateIp4TagException
     */
    #[Post(
        description: "Create a new IP4 Tag with the provided data.",
        summary: "Create a new IP4 Tag",
        security: [["Bearer" => []]],
        tags: ["IP4 Tag"],
        responses: [
            "201" => new OAResponse(
                response: "201",
                description: "Created",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["uuid" => "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e"],
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

    #[Parameter(
        name: "id_ip",
        description: "The ID of the IP4.",
        in: "query",
        required: true,
        schema: new Schema(type: "integer"),
        example: 5,
    )]

    #[Parameter(
        name: "tag",
        description: "The IP4 Tag.",
        in: "query",
        required: true,
        schema: new Schema(type: "string"),
        example: "Reserved",
    )]

    #[RequestBody(
        description: "Provide the IP4 Tag data to create a new IP4 Tag.",
        required: true,
        content: new MediaType(
            mediaType: "application/json",
            schema: new Schema(
                ref: new Model(type: CreateIP4TagRequest::class),
                schema: "CreateIP4TagRequest",
                title: "Create IP4 Tag Request",
                description: "The IP4 Tag data to create a new IP4 Tag.",
                required: ["tag"],
                type: "object"
            ),
        ),
    )]
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/v1/ip4_tag', name: 'create_ip4_tag', methods: ['POST'])]
    public function __invoke(CreateIP4TagRequest $request): JsonResponse
    {
        try {
            $this->logger->info("Create IP4 Tag Request", ["request" => $request]);
            $tagUUID = new Ip4TagUuid(UuidValueObject::random()->value());
            $tagUuidIp = new Ip4TagUuidIp($request->getUuidIp());
            $tagTag = new Ip4TagTag($request->getTag());
            $ip4Tag = new Ip4Tag($tagUUID, $tagUuidIp, $tagTag);
            $uuid = $this->createIP4Tag->__invoke($ip4Tag);

            return new JsonResponse(["uuid" => $uuid], Response::HTTP_CREATED);
        } catch (Exception $e) {
                $this->logger->error('Exception:( -> ' . $e->getMessage());
                $vmException = new Ip4TagsExceptions();
                return $vmException($e);
        }
        
    }
}