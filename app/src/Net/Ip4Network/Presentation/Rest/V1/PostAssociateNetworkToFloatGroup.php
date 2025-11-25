<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Presentation\Rest\V1;

use Error;

use GridCP\Net\Ip4Network\Application\Requests\AssociateIPNetworkFloatGroupRequest;
use GridCP\Net\Ip4Network\Application\Services\CreateAssociateIPNetworkFloatGroup;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorNetworkNotExist;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
use GridCP\Net\Ip4Network\Domain\VO\FloatGroupUuuid;
use InvalidArgumentException;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
final class PostAssociateNetworkToFloatGroup extends AbstractController
{
    public function __construct(private readonly CreateAssociateIPNetworkFloatGroup $associateNetworkFloatGroup, private readonly LoggerInterface $logger)
    {
    }

    #[Post(
        description: "Associated ip network to one float group.",
        summary: "Associated ip network to one float group",
        security: [["Bearer" => []]],
        tags: ["IP4 Network"],
        responses: [
            "204" => new OAResponse(
                response: "204",
                description: "Update",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
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
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Error input data. GridCP Validation Failed :("],// NOSONAR
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "404",
                description: "Not Found",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Not Found"],// NOSONAR
                    ),
                ),
            ),
            "409" => new OAResponse(
                response: "409",
                description: "Conflict",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Associate Network To Float Group"],// NOSONAR
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
                        example: ["error" => "Internal Server Error."],// NOSONAR
                    ),
                ),
            ),
        ],
    )]
    
    #[RequestBody(
        description: "Provide Associate Network To Float Group",
        required: true,
        content: new MediaType(
            mediaType: "application/json",// NOSONAR
            schema: new Schema(
                ref: new Model(type: AssociateIPNetworkFloatGroupRequest::class),
                schema: "AssociateIPNetworkFloatGroup",
                title: "Associate Network To Float Group",
                description: "Request schema for creating a Associate Network To Float Group.",
                required: ["uuid"],
                type: "object"
            )
        )
    )]
        #[IsGranted('ROLE_ADMIN')]

    #[Route('/v1/ip4_network/{uuid}/add_float_group', name: 'create_ip4_network_float_group', methods: ['POST'])]
    public function __invoke(string $uuid, AssociateIPNetworkFloatGroupRequest $request): JsonResponse
    {
        try {
            $uuidNetwork = new Ip4NetworkUUID($uuid);
            $floatGroupUuid = new FloatGroupUuuid($request->getuuid());
            $this->logger->info("Start Associate Network with Uuid: " .$uuidNetwork->value()." To Float Group with Uuid: " . $floatGroupUuid->value());

            $this->associateNetworkFloatGroup->__invoke($uuidNetwork, $floatGroupUuid);
            $result = new JsonResponse(null, status: Response::HTTP_NO_CONTENT);
            
        } catch(ErrorFloatGroupNotExist $e){
            $this->logger->error("Error float Group Not Exist -> UUid: ". $request->getuuid() ." :( ->". $e->getMessage());// NOSONAR
            $result = new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch(ErrorNetworkNotExist $e){
            $this->logger->error("Error Network Not Exist -> UUid: ". $uuid ." :( ->". $e->getMessage());// NOSONAR
            $result = new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_NOT_FOUND);
        }catch(InvalidArgumentException $e){
            $this->logger->error("Error in inputu data  :( ->". $e->getMessage());// NOSONAR
            $result = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch(\Exception $e){
            $this->logger->error("Error in Associate Network To Float Group". $uuid . ' - '. $request->getuuid() ." :( ->". $e->getMessage());// NOSONAR
            $result = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);

        }
        return $result;
    }
}