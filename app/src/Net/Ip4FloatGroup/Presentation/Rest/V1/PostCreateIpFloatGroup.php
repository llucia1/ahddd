<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Presentation\Rest\V1;

use Error;
use FOS\RestBundle\Controller\Annotations\Route;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Ip4FloatGroup\Application\Request\CreateIpFloatGroupRequest;
use GridCP\Net\Ip4FloatGroup\Application\Service\CreateIp4FloatGroup;

use GridCP\Net\Ip4FloatGroup\Domain\Exception\IP4FloatGroupDuplicated;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroups;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsActive;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsName;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsUuid;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
class PostCreateIpFloatGroup extends AbstractController
{
    public function __construct(
        private readonly CreateIp4FloatGroup $createIp4FloatGroup,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Post(
        description: "Create a new IP4 Float Group with the given data.",
        summary: "Create a new IP4 Float Group",
        security: [["Bearer" => []]],
        tags: ["Float Group"],
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
            "400" => new OAResponse(
                response: "400",
                description: "Bad Request",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["{'message':'GridCP Validation Failed :(','errors':[{'property:name,value:null,message:Name should not be blank}]}'"],
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
                        example: ["{'error':'Ip4 Float Group Duplicated -> prueba22'}"],
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
                        example: ["error" => "Internal Server Error."],
                    ),
                ),
            ),
        ],
    )]

    #[Parameter(
        name: "name",
        description: "The name of the IP Float Group.",
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            format: "ipv4",
            example: "prueba22"
        ),
    )]
    
    #[RequestBody(
        description: "Provide the IP4 Float Group data to create a new IP4 Float Group.",
        required: true,
        content: new MediaType(
            mediaType: "application/json",
            schema: new Schema(
                ref: new Model(type: CreateIpFloatGroupRequest::class)
            )
        )
    )]
        
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/float_group', name: 'create_ip_float_group', methods: ['POST'])]
    public function __invoke(CreateIpFloatGroupRequest $request): JsonResponse
    {
        try
        {
            $this->logger->info('Start creating IP4 Float Group: ' . $request->getName());
            $ipFloatGroupUuid = new Ip4FloatGroupsUuid(UuidValueObject::random()->value());
            $ipFloatGroupName = new Ip4FloatGroupsName($request->getName());
            $ipFloatGroupActive = new Ip4FloatGroupsActive(true);
            $ipFloatGroup = new Ip4FloatGroups($ipFloatGroupUuid, $ipFloatGroupName, $ipFloatGroupActive);
            $uuid = $this->createIp4FloatGroup->__invoke($ipFloatGroup);
            return $this->json(['uuid' => $uuid], Response::HTTP_CREATED);
        } catch (IP4FloatGroupDuplicated $e) {
            $this->logger->error('Error creating IP4 Float Group: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch(Error $e){
            $this->logger->error("Error creating IP4 Float Group ".$request->getName()." :( ->". $e->getMessage());
           return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}