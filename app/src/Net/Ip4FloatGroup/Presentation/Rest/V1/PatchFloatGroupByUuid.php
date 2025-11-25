<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Presentation\Rest\V1;

use GridCP\Net\Ip4FloatGroup\Application\Request\PatchFloatGroupRequest;
use GridCP\Net\Ip4FloatGroup\Application\Service\PatchFloatGroupService;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ErrorUuidInvalid;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsName;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsPacth;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsUuid;


use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
final class PatchFloatGroupByUuid extends AbstractController
{
    public function __construct(
        private readonly PatchFloatGroupService $pathFloatGroupService,
        private readonly LoggerInterface  $logger
    ){}

    #[Patch(
        description: "Update Float Group by id with the provided data.",
        summary: "Update Float Group",
        security: [["Bearer" => []]],
        tags: ["Float Group"],
        responses: [
            "204" => new OAResponse(
                response: "204",
                description: "Update",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [""],
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
                        example: ["error" => "Bad Request"],
                    ),
                ),
            ),
            "401" => new OAResponse(
                response: "401",
                description: "Unauthorized",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Unauthorized"],
                    ),
                ),
            ),
            "403" => new OAResponse(
                response: "403",
                description: "Forbidden",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Forbidden"],
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "404",
                description: "Not Found Float Group",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Not Found"],
                    ),
                ),
            ),
            "409" => new OAResponse(
                response: "409",
                description: "Float Group already exists. Duplicate",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Float Group already exists. Duplicate"],
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


    #[RequestBody(
        description: "Provide the Float Group data to update a Float Group.",
        required: true,
        content: new MediaType(
            mediaType: "application/json",
            schema: new Schema(
                ref: new Model(type: PatchFloatGroupRequest::class )
            )
        ),
    )]

    #[Parameter(
        name: "uuid",
        description: "The UUID of the Float Group.",
        in: "path",
        required: true,
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e",
    )]
        
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/float_group/{uuid}', name: 'path_float_group_by_id', methods: ['PATCH'])]
    public function __invoke(PatchFloatGroupRequest $request, string $uuid): JsonResponse

    {
        try {

            $this->logger->info('Start update Float Group by uuid -> ' . $uuid);
            $uuid = new Ip4FloatGroupsUuid($uuid);  
            $name = ($request->name() !== null)? new Ip4FloatGroupsName($request->name()) : null;
            $floatGroup = new Ip4FloatGroupsPacth($uuid,$name);
            $this->pathFloatGroupService->__invoke($floatGroup);
            return $this->json(null, status: Response::HTTP_NO_CONTENT);

        } catch (ErrorUuidInvalid $e) {
            $this->logger->error("Invalid UUID provided:" . $uuid);
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ErrorFloatGroupNotExist $e) {
            $this->logger->error("Float Group not found Error: " . $request->name() . " :( ->" . $e->getMessage());
            return  $this->json(["error"=>$e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (HttpException $e){
            $this->logger->error(`Error in modify Float Group {$uuid} :( -> ` . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }
}