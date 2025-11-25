<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Presentation\Rest\V1;

use Error;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NotValid;
use GridCP\Net\Ip4Network\Application\Requests\CreateIPNetworkRequest;
use GridCP\Net\Ip4Network\Application\Services\CreateIPNetwork;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorFloatGroupOrNetworkNotExist;
use GridCP\Net\Ip4Network\Domain\Exception\IP4NetworkDuplicated;
use GridCP\Net\Ip4Network\Domain\VO\Ip4Network;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkActive;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkBroadcast;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkFree;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkGateway;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkName;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNameServer;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNetMask;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNoArp;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkPriority;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkRir;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkSelectableByClient;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
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

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
final class PostCreateIPNetwork extends AbstractController
{
    public function __construct(private readonly CreateIPNetwork $createIPNetwork, private readonly LoggerInterface $logger)
    {
    }

    #[Post(
        description: "Create a new IP4 Network with the provided data.",
        summary: "Create a new IP4 Network",
        security: [["Bearer" => []]],
        tags: ["IP4 Network"],
        responses: [
            "201" => new OAResponse(
                response: "201",
                description: "Created",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
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
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Input data Not Valid."],// NOSONAR
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
                        example: ["error" => "Ip4 Network Duplicated"],// NOSONAR
                    ),
                ),
            ),
            "509" => new OAResponse(
                response: "509",
                description: "Conflict",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Gataway Not Valid"],// NOSONAR
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

    #[Parameter(
        name: "name",
        description: "The name of the IP network.",
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            format: "ipv4",
            example: "Ipnetwork"
        ),
    )]

    #[Parameter(
        name: "name_server1",
        description: "The first name server IP address in IPv4 format.",
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            format: "ipv4",
            example: "192.168.1.1"
        ),
    )]

    #[Parameter(
        name: "name_server2",
        description: "The second name server IP address in IPv4 format.",
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            format: "ipv4",
            example: "192.168.1.2"
        ),
    )]

    #[Parameter(
        name: "name_server3",
        description: "The third name server IP address in IPv4 format.",
        in: "query",
        required: false,
        schema: new Schema(
            type: "string",
            format: "ipv4",
            example: "192.168.1.3"
        ),
    )]

    #[Parameter(
        name: "name_server4",
        description: "The fourth name server IP address in IPv4 format.",
        in: "query",
        required: false,
        schema: new Schema(
            type: "string",
            format: "ipv4",
            example: "192.168.1.4"
        ),
    )]

    #[Parameter(
        name: "priority",
        description: "The priority of the IP network.",
        in: "query",
        required: true,
        schema: new Schema(
            type: "integer",
            example: 50
        ),
    )]


    #[Parameter(
        name: "netmask",
        description: "The netmask in IPv4 format.",
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            format: "ipv4",
            example: "255.255.255.0"
        ),
    )]

    #[Parameter(
        name: "gateway",
        description: "The gateway IP address in IPv4 format.",// NOSONAR
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            format: "ipv4",
            example: "192.168.1.254"
        ),
    )]

    #[Parameter(
        name: "broadcast",
        description: "The broadcast IP address in IPv4 format.",// NOSONAR
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            format: "ipv4",
            example: "192.168.1.255"
        ),
    )]

    #[RequestBody(
        description: "Provide the IP4 Network data to create a new IP4 Network.",
        required: true,
        content: new MediaType(
            mediaType: "application/json",// NOSONAR
            schema: new Schema(
                ref: new Model(type: CreateIPNetworkRequest::class),
                schema: "CreateIPNetworkRequest",
                title: "Create IP Network Request",
                description: "Request schema for creating a new IP network.",
                required: ["name", "netmask", "gateway", "broadcast"],
                type: "object"
            )
        )
    )]
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/v1/ip4_network', name: 'create_ip4_network', methods: ['POST'])]
    public function __invoke(CreateIPNetworkRequest $request): JsonResponse// NOSONAR
    {
        try {
            $this->logger->info("Start creating IP Network: " . $request->getName());
            
            $uuid = $this->createIPNetwork->__invoke($this->setVo($request));
            $result = new JsonResponse(['uuid' => $uuid], Response::HTTP_CREATED);
        }catch(ErrorFloatGroupOrNetworkNotExist|ErrorFloatGroupNotExist $e) {
            $this->logger->error("Error in create IP4_NETWORK" . $request->getName() . " :( ->" . $e->getMessage());// NOSONAR
            $result = new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch(IP4NetworkDuplicated $e){
            $this->logger->error("Error in create IP4_NETWORK".$request->getName()." :( ->". $e->getMessage());// NOSONAR
            $result = new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        }catch(Ip4NotValid $e){
            $this->logger->error("Error in create IP4_NETWORK".$request->getName()." :( ->". $e->getMessage());// NOSONAR
            $result = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }catch(\InvalidArgumentException $e){
            $this->logger->error("Error in inputu data ".$request->getName()." :( ->". $e->getMessage());// NOSONAR
            $result = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }  catch(\Exception $e){
            $this->logger->error("Error in create IP4_NETWORK".$request->getName()." :( ->". $e->getMessage());// NOSONAR
            $result = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $result;
    }


    private function setVo( CreateIPNetworkRequest $request ): Ip4Network
    {
        $ipNetworkUUID = new Ip4NetworkUUID(UuidValueObject::random()->value());
        $ip4NetworkName = new Ip4NetworkName($request->getName());
        $ip4Gateway = new Ip4NetworkGateway($request->getGateway());
        $ipBroadCast = new Ip4NetworkBroadcast($request->getBroadcast());
        $ipPriority = new Ip4NetworkPriority($request->getPriority());
        $ipNetworkNetMask = new Ip4NetworkNetMask($request->getNetmask());
        $ipNetworkNameServer = new Ip4NetworkNameServer($request->getNameServer1());
        $ipNetworkNameServer_2 = new ip4NetworkNameServer($request->getNameServer2());
        $ipNetworkNameServer_3 = ($request->getNameServer3())? new ip4NetworkNameServer($request->getNameServer3()) : null;
        $ipNetworkNameServer_4 = ($request->getNameServer4())? new ip4NetworkNameServer($request->getNameServer4()) : null;
        $ip4NetworkActive = new Ip4NetworkActive(true);

        return new Ip4Network($ipNetworkUUID, $ip4NetworkName,
            $ipNetworkNameServer, $ipNetworkNameServer_2, $ipNetworkNameServer_3, $ipNetworkNameServer_4, $ipPriority,
            new Ip4NetworkSelectableByClient(true),new Ip4NetworkFree(0),$ipNetworkNetMask, $ip4Gateway, $ipBroadCast, new Ip4NetworkNoArp(false), new Ip4NetworkRir(false),
            $ip4NetworkActive);
    }
}