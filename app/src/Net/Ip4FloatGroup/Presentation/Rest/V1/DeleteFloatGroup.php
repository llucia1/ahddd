<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Presentation\Rest\V1;

use Error;
use FOS\RestBundle\Controller\Annotations\Route;
use GridCP\Net\Ip4FloatGroup\Application\Service\DisableFloatGroup;
use GridCP\Net\Ip4FloatGroup\Application\Service\FloatGroupByUUIDService;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ErrorUuidInvalid;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\FloatGroupHasAssociatedNetworks;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\FloatGroupHasAssociatedNodes;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsUuid;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
class DeleteFloatGroup extends AbstractController
{
    public function __construct(
        private readonly FloatGroupByUUIDService $Ip4FloatGroupByUuid,
        private readonly DisableFloatGroup $disableIp4FloatGroup,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Delete(
        description: "Disable Float Group with.",
        summary: "Disable Float Group",
        security: [["Bearer" => []]],
        tags: ["Float Group"],
        responses: [
            "204" => new OAResponse(
                response: "204",
                description: "Disable Float Group",
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
                description: "Not Found Node",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Not Found"],
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
        description: "The UUID of the Float Group.",
        in: "path",
        required: true,
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e",
    )]
    
    #[IsGranted('ROLE_ADMIN')]    #[Route('/v1/float_group/{uuid}', name: 'patch_ip_float_group_disable', methods: ['DELETE'])]
    public function __invoke(string $uuid): JsonResponse
    {
        try
        {
            $this->logger->info('Start disable Float Group: ' . $uuid);
            $ipFloatGroupUuid = new Ip4FloatGroupsUuid($uuid);

            $this->disableIp4FloatGroup->__invoke($ipFloatGroupUuid);

            return $this->json(null, status: Response::HTTP_NO_CONTENT);
        } catch (ErrorUuidInvalid $e) {
                    $this->logger->error("Invalid UUID provided:" . $uuid);
                    return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (FloatGroupHasAssociatedNetworks $e) {
            $this->logger->error('Error Disable Float Group: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (FloatGroupHasAssociatedNodes $e) {
            $this->logger->error('Error Disable Float Group: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (ErrorFloatGroupNotExist $e) {
            $this->logger->error('Error Disable Float Group: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch(Error $e){
            $this->logger->error("Error Disable Float Group ".$uuid." :( ->". $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}