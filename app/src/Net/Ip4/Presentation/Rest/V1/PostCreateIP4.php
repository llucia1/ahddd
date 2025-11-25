<?php

declare(strict_types=1);

namespace GridCP\Net\Ip4\Presentation\Rest\V1;

use Error;
use GridCP\Common\Domain\Const\NotSet;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Ip4\Application\CreateIP4Request;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Service\CreateIP4;
use GridCP\Net\Ip4\Domain\Exceptions\CreateIpException;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4Duplicated;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4AreAsignnedException;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NotValid;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4sInSubnetsException;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExist;
use GridCP\Net\Ip4\Domain\VO\Ip4;
use GridCP\Net\Ip4\Domain\VO\Ip4Active;
use GridCP\Common\Domain\ValueObjects\Ip4TagTag;
use GridCP\Net\Ip4\Domain\Exceptions\MaskNotValid;
use GridCP\Net\Ip4\Domain\VO\Ip4Ip;
use GridCP\Net\Ip4\Domain\VO\Ip4Priority;
use GridCP\Net\Ip4\Domain\VO\Ip4Uuid;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Lambdish\Phunctional\map;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_v1_')]
final class PostCreateIP4 extends AbstractController
{
    public function __construct(
        private readonly CreateIP4       $createIP4,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Post(
        description: "Create a new IP4 record with the provided data.",
        summary: "Create a new IP4 Record",
        security: [["Bearer" => []]],
        tags: ["IP4"],
        responses: [
            "201" => new OAResponse(
                response: "201",
                description: "Created",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [ "uuid" => "faf0a747-9f4b-4640-91c7-686103afd887" ],
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
            "401" => new OAResponse(
                response: "401",
                description: "Unauthorized",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Conflict"],
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
        name: "ip",
        description: "The IP address.",
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            example: "192.168.1.1"
        ),
    )]

    #[Parameter(
        name: "uuid_network",
        description: "The network Uuid.",
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e"
        ),
    )]

    #[Parameter(
        name: "priority",
        description: "The Priority.",
        in: "query",
        required: true,
        schema: new Schema(
            example: "8",
            type: "integer",
            minimum: 0,
            maximum: 10
        ),
    )]

    #[RequestBody(
        description: "Provide the IP4 Network data to create a new IP4 Network.",
        required: true,
        content: new MediaType(
            mediaType: "application/json",
            schema: new Schema(
                ref: new Model(type: CreateIP4Request::class),
                schema: "CreateIP4Request",
                title: "Create IP4 Request",
                description: "Request schema for creating a new IP4 record.",
                required: ["ip", "id_network"],
                type: "object"
            )
        ),
    )]
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4', name: 'ip4_create', methods: ['POST'])]
    public function __invoke(CreateIP4Request $request): JsonResponse
    {
        try {
            $this->logger->info("Start creating a new IP4 record: " . $request->getIP());
            $ip4UUID = new Ip4Uuid(UuidValueObject::random()->value());
            $ip4Ip = new Ip4Ip($request->getIP());
            $ip4IdNetwork = new Ip4UuidNetwork($request->getUuidNetwork());
            $ip4Active = new Ip4Active(true);
            $priority = new Ip4Priority($request->getPriority());
            $tag = new Ip4TagTag($request->getTag() ? $request->getTag() : NotSet::VALUE  );
            $ip4 = new Ip4($ip4UUID, $ip4Ip, $ip4IdNetwork, $ip4Active, $priority, $tag);
            $ip4s = $this->createIP4->__invoke($ip4);
            return new JsonResponse(
                map(
                    fn(Ip4Response $ip4Response): array => [
                        "uuid" => $ip4Response->uuid(),
                    ],
                    $ip4s
                ),
                Response::HTTP_CREATED
            );
            
        } catch (Ip4Duplicated $e) {
            $this->logger->error("IP4 Duplicated not valid. :( ->" . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        }
         catch (MaskNotValid $e) {
            $this->logger->error("IP4 Mask not valid. :( ->" . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        } catch (InvalidArgumentException $e){
            $this->logger->error('Uuid Not Valid:( -> ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }  catch (NerworkNoExist $e) {
            $this->logger->error('The id_network does not exist, id_network: ' . $request->getUuidNetwork() . " :( ->" . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_NOT_FOUND);
        }   catch (Ip4AreAsignnedException $e) {
            $this->logger->error('The following Ips are assigned (Genuine Ip). Cannot be changed: ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        }  catch (Ip4sInSubnetsException $e) {
            $this->logger->error('The following Ips are a one Subnet. Cannot be changed: ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        } catch (ListIp4EmptyException $e) {
            $this->logger->error('No IPs to insert. ' . $request->getUuidNetwork() . " :( ->" . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        } catch (Error $e) {
            $this->logger->error("Error in Create IP4: " . $request->getIP()." :( ->" . $e->getMessage());
            throw new CreateIpException($e);
        }
    }
}