<?php

declare(strict_types=1);

namespace GridCP\Net\Ip4\Presentation\Rest\V1;

use Error;
use GridCP\Common\Domain\ValueObjects\Ip4TagTag;
use GridCP\Net\Ip4\Application\CreateIP4Request;
use GridCP\Net\Ip4\Application\PacthIP4Request;
use GridCP\Net\Ip4\Application\Service\EditIP4service;
use GridCP\Net\Ip4\Domain\Exceptions\CreateIpException;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4AreAsignnedException;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4Duplicated;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NotFound;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NotValid;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4sInSubnetsException;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExist;
use GridCP\Net\Ip4\Domain\VO\Ip4Ip;
use GridCP\Net\Ip4\Domain\VO\Ip4Priority;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;
use GridCP\Net\Ip4\Domain\VO\PatchIp4Vo;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Patch;
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
final class PatchIP4 extends AbstractController
{
    public function __construct(
        private readonly EditIP4service       $editIp4,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Patch(
        description: "Patch an IP4.",
        summary: "Edit an IP4",
        security: [["Bearer" => []]],
        tags: ["IP4"],
        responses: [
            "204" => new OAResponse(
                response: "204",
                description: "Edited",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                                     "status" => "Ip4 network changed: 128.1.8.0/30"
                                ]
                    ),
                )
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

    #[RequestBody(
        description: "Provide the IP4 Network data to edit IP4 Network.",
        required: true,
        content: new MediaType(
            mediaType: "application/json",
            schema: new Schema(
                ref: new Model(type: PacthIP4Request::class),
                schema: "CreateIP4Request",
                title: "Edit IP4 Request",
                description: "Request schema for edit a IP4 record.",
                required: ["ip", "id_network"],
                type: "object"
            )
        ),
    )]
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4', name: 'ip4_edti', methods: ['PATCH'])]
    public function __invoke(PacthIP4Request $request): JsonResponse
    {
        try {
            $this->logger->info("Start edit a IP4 record: " . $request->getIP());

            $ip4Ip = is_null($request->getIP())? null : new Ip4Ip($request->getIP());
            $ip4IdNetwork = is_null($request->getUuidNetwork())? null : new Ip4UuidNetwork($request->getUuidNetwork());
            $priority = is_null($request->getPriority())? null : new Ip4Priority($request->getPriority());
            $tag = is_null($request->getTag()) ?  null : new Ip4TagTag($request->getTag()   );


            $ip4 = new PatchIp4Vo($ip4Ip, $ip4IdNetwork, $priority, $tag );
            $notFound = $this->editIp4->__invoke($ip4);

            if ($notFound && !empty($notFound)) {
                throw new Ip4NotFound(
                                         array_reduce($notFound, fn($ipsTxt, $ipTxt) => $ipsTxt . $ipTxt, '')
                                     );
            }


            return new JsonResponse(['status' => 'Ip4 network changed: '.$ip4Ip->value()], Response::HTTP_NO_CONTENT);

            
        } catch (Ip4NotValid $e) {
            $this->logger->error("IP4 record already exists: " . $request->getIP() . " ->" . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Ip4NotFound $e) {
            return new JsonResponse(['error' => $notFound], Response::HTTP_NOT_FOUND);
        } catch (InvalidArgumentException $e){
            $this->logger->error('Uuid Not Valid:( -> ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }  catch (Ip4AreAsignnedException $e) {
            $this->logger->error('The following Ips are assigned (Genuine Ip). Cannot be changed: ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        }  catch (Ip4sInSubnetsException $e) {
            $this->logger->error('The following Ips are a one Subnet. Cannot be changed: ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        }  catch (NerworkNoExist $e) {
            $this->logger->error('The id_network does not exist, id_network: ' . $request->getUuidNetwork() . " :( ->" . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_NOT_FOUND);
        } 
    }
}