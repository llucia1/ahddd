<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Presentation\Rest\V1;

use Error;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NotValid;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4Network\Application\Requests\PatchIPNetworkRequest;
use GridCP\Net\Ip4Network\Application\Services\PatchIPNetworkService;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorFloatGroupOrNetworkNotExist;
use GridCP\Net\Ip4Network\Domain\Exception\IP4NetworkDuplicated;
use GridCP\Net\Ip4Network\Domain\Exception\NetworkNotExistException;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkActive;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkBroadcast;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkFree;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkGateway;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkName;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNameServer;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNetMask;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNoArp;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkPatch;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkPriority;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkRir;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkSelectableByClient;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
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
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
final class PatchIPNetwork extends AbstractController
{
    public function __construct(private readonly PatchIPNetworkService $patchIPNetwork, private readonly LoggerInterface $logger)
    {
    }

    #[Patch(
        description: "Edit a IP4 Network with the provided data.",
        summary: "Edit a IP4 Network",
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
                        example: ["error" => "Bad Request"],// NOSONAR
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "404",
                description: "Not Found Node",
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
                        example: ["{'error':'Ip4 Network Duplicated -> prueba22'}"],// NOSONAR
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
        description: "Provide the IP4 Network data to edit a new IP4 Network.",
        required: true,
        content: new MediaType(
            mediaType: "application/json",// NOSONAR
            schema: new Schema(
                ref: new Model(type: PatchIPNetworkRequest::class )
            )
        ),
    )]
    #[Parameter(
        name: "uuid",
        description: "The UUID of the IP4 Network to delete.",
        in: "path",
        required: true,
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e",
    )]
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4_network/{uuid}', name: 'patch_ip4_network', methods: ['PATCH'])]
    public function __invoke(PatchIPNetworkRequest $request, string $uuid): JsonResponse
    {
        try {
            $this->logger->info("Start Edit IP Network: " . $request->getName());
            $ipNetworkUUID = new Ip4NetworkUUID($uuid);

            $this->patchIPNetwork->__invoke($this->setVo($request, $ipNetworkUUID), $ipNetworkUUID->value());
            $resutl = $this->json(null, status: Response::HTTP_NO_CONTENT);

        } catch(NetworkNotExistException $e){
            $this->logger->error("In patch IP4_NETWORK Not Exits".$request->getName()." :( ->". $e->getMessage());// NOSONAR
            $resutl = $this->json(["error"=>$e->getMessage()], Response::HTTP_NOT_FOUND);
        }catch(ErrorFloatGroupOrNetworkNotExist|ErrorFloatGroupNotExist $e) {
            $this->logger->error("Error in create IP4_NETWORK" . $request->getName() . " :( ->" . $e->getMessage());// NOSONAR
            $resutl = new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch(IP4NetworkDuplicated $e){
            $this->logger->error("Error in create IP4_NETWORK".$request->getName()." :( ->". $e->getMessage());// NOSONAR
            $resutl = new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        }catch(Ip4NotValid $e){
            $this->logger->error("Error in create IP4_NETWORK".$request->getName()." :( ->". $e->getMessage());// NOSONAR
            $resutl = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }catch(\InvalidArgumentException $e){
            $this->logger->error("Error in inputu data ".$request->getName()." :( ->". $e->getMessage());// NOSONAR
            $resutl = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch(\Exception $e){
            $this->logger->error("Error in patch IP4_NETWORK".$request->getName()." :( ->". $e->getMessage());// NOSONAR
            $resutl = $this->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        return $resutl;
    }
    private function setVo( PatchIPNetworkRequest $request, Ip4NetworkUUID $uuid ): Ip4NetworkPatch
    {
        $ip4NetworkName = (!is_null($request->getName()))? new Ip4NetworkName($request->getName()) : null;
        $ip4Gateway = (!is_null($request->getGateway()))? new Ip4NetworkGateway($request->getGateway()) : null;
        $ipBroadCast = (!is_null($request->getBroadcast()))? new Ip4NetworkBroadcast($request->getBroadcast()) : null;
        $ipPriority = (!is_null($request->getPriority()))? new Ip4NetworkPriority($request->getPriority()) : null;
        $ipFree = (!is_null($request->getFree()))? new Ip4NetworkFree($request->getFree()) : null;
        $ipNetworkNetMask = (!is_null($request->getNetmask()))? new Ip4NetworkNetMask($request->getNetmask()) : null;
        $ipNetworkNameServer = (!is_null($request->getNameServer1()))? new Ip4NetworkNameServer($request->getNameServer1()) : null;
        $ipNetworkNameServer_2 = (!is_null($request->getNameServer2()))? new ip4NetworkNameServer($request->getNameServer2()) : null;
        $ipNetworkNameServer_3 = (!is_null($request->getNameServer3()))? new ip4NetworkNameServer($request->getNameServer3()) : null;
        $ipNetworkNameServer_4 = (!is_null($request->getNameServer4()))? new ip4NetworkNameServer($request->getNameServer4()) : null;
        $ip4NetworkActive = (!is_null($request->getActive()))? new Ip4NetworkActive($request->getActive()) : null;
        $ip4NetworkNoArp = (!is_null($request->getNo_arp()))? new Ip4NetworkNoArp($request->getNo_arp()) : null;
        $ip4NetworkRir = (!is_null($request->getRir()))? new Ip4NetworkRir($request->getRir()) : null;
        $ip4NetworkNoSelectableByClient = (!is_null($request->getSelectable_by_client()))? new Ip4NetworkSelectableByClient($request->getSelectable_by_client()) : null;

        return new Ip4NetworkPatch(
                                            $uuid,
                                            $ip4NetworkName,
                                            $ipNetworkNameServer,
                                            $ipNetworkNameServer_2,
                                            $ipNetworkNameServer_3,
                                            $ipNetworkNameServer_4,
                                            $ipPriority,
                                            $ip4NetworkNoSelectableByClient,
                                            $ipFree,
                                            $ipNetworkNetMask,
                                            $ip4Gateway,
                                            $ipBroadCast,
                                            $ip4NetworkNoArp,
                                            $ip4NetworkRir,
                                            $ip4NetworkActive
                                        );
    }

}