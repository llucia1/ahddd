<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Presentation\Rest\V1;

use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Service\DeleteAllIp4OfNetwork;
use GridCP\Net\Ip4\Application\Service\ListIp4ByNetworkUuid;
use GridCP\Net\Ip4\Domain\Exceptions\GetIP4Exception;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4AreAsignnedException;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4sInSubnetsException;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;
use GridCP\Net\Ip4\Domain\Service\IListIp4ByNetworkUuid;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;
use InvalidArgumentException;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Parameter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes\Response as OAResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Lambdish\Phunctional\map;

#[Route('/api', name: 'api_v1_')]
class DeleteIp4AllByNetworkUuid extends AbstractController
{
    public function __construct(private readonly DeleteAllIp4OfNetwork $deleteAllIp4OfNetwork, private readonly LoggerInterface $logger)
    {
    }

    #[Delete(
        description: "Delete all IP4 of one IP4Network.",
        summary: "Delete all IP4 of one IP4Network",
        security: [["Bearer" => []]],
        tags: ["IP4"],
        responses: [
            "204" => new OAResponse(
                response: "204",
                description: "Deleted",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: [
                                     "status" => "Ip4's has been deleted"// NOSONAR
                                ]
                    ),
                )
            ),
            "400" => new OAResponse(
                response: "400",
                description: "Bad Request",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "IP4 not Valid",// NOSONAR
                        ],
                    ),
                ),
            ),
            
            "404" => new OAResponse(
                response: "404",
                description: "Not Content",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "TNot Found",// NOSONAR
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
                        example: ['error'=>'Not Found Ip4s'],// NOSONAR
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
                        example: [
                            "error" => "An error occurred while fetching IP4s",// NOSONAR
                        ],
                    ),
                ),
            ),
        ],
    )]

    #[Parameter(
        name: "uuid",
        description: "Uuid network to delete all related ips.",
        in: "path",
        required: true,
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e",
    )]    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4/ip4network/{uuid}', name: 'delete_all_ip4_by_network_uuid', methods: ['DELETE'])]
    public function __invoke(string $uuid): JsonResponse{
        try {
            $this->logger->info("Delete all IP4 By Netwok Uuid");
            $ip4IdNetwork = new Ip4UuidNetwork($uuid);

            $this->deleteAllIp4OfNetwork->__invoke($ip4IdNetwork);
            return new JsonResponse(['status' => "Ip4's has been deleted"], Response::HTTP_NO_CONTENT);
        }catch (ListIp4EmptyException $e){
            $this->logger->error($e->getMessage());// NOSONAR
            $result = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);// NOSONAR
        }    catch (Ip4AreAsignnedException $e) {
            $this->logger->error('The following Ips are assigned (Genuine Ip). Cannot be changed: ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        }  catch (Ip4sInSubnetsException $e) {
            $this->logger->error('The following Ips are a one Subnet. Cannot be changed: ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        } catch (InvalidArgumentException $e){
            $this->logger->error('Uuid Not Valid:( -> ' . $e->getMessage());// NOSONAR
            $result = new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);// NOSONAR
        } catch (NerworkNoExistException $e){
            $this->logger->error($e->getMessage());// NOSONAR
            $result = new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);// NOSONAR
        }
        return $result;
    }

}