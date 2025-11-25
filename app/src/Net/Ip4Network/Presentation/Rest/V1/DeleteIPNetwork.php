<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Presentation\Rest\V1;

use GridCP\Net\Ip4Network\Application\Services\DeleteIPNetworkService;
use GridCP\Net\Ip4Network\Domain\Exception\HasIp4sNetworkException;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
use InvalidArgumentException;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Parameter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
class DeleteIPNetwork extends AbstractController
{
    const APPLICATION_JSON = "application/json";
    const ERROR = "error";
    const STATUS = "status";

    public function __construct(private readonly DeleteIPNetworkService $deleteIPNetworkService, private readonly LoggerInterface $logger)
    {
    }

    #[Delete(
        description: "Delete an IP4 Network with the given UUID.",
        summary: "Delete an IP4 Network",
        security: [["Bearer" => []]],
        tags: ["IP4 Network"],
        responses: [
            "200" => new OAResponse(
                response: "200",
                description: "Deleted",
                content: new MediaType(
                    mediaType: self::APPLICATION_JSON,
                    schema: new Schema(
                        type: "object",
                        example: [self::STATUS => "192.168.0.1/24 has been deleted"],// NOSONAR
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "404",
                description: "Network not found",
                content: new MediaType(
                    mediaType: self::APPLICATION_JSON,
                    schema: new Schema(
                        type: "object",
                        example: [""],
                    ),
                ),
            ),
            "409" => new OAResponse(
                response: "204",
                description: "No Content",
                content: new MediaType(
                    mediaType: self::APPLICATION_JSON,
                    schema: new Schema(
                        type: "object",
                        example: [self::ERROR => "This Network has associated Ip4s"],
                    ),
                ),
            ),
            "500" => new OAResponse(
                response: "500",
                description: "Internal Server Error",
                content: new MediaType(
                    mediaType: self::APPLICATION_JSON,
                    schema: new Schema(
                        type: "object",
                        example: [self::ERROR => "Internal Server Error"],
                    ),
                ),
            ),
        ],
    )]

    #[Parameter(
        name: "uuid",
        description: "The UUID of the IP4 Network to delete.",
        in: "path",
        required: true,
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e",
    )]
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4_network/{uuid}', name: 'delete_ip4_network', methods: ['DELETE'])]
    public function __invoke(string $uuid): JsonResponse
    {
        try {
            $networkUuid = new Ip4NetworkUUID($uuid);
            $this->logger->info('Deleting IP4 Network with UUID: ' . $uuid);
            $this->deleteIPNetworkService->__invoke($networkUuid);
            $result = new JsonResponse([self::STATUS => $uuid . ' has been deleted'], Response::HTTP_OK);
        }catch (ListIp4NetworkEmptyException $e){
            $this->logger->error($e->getMessage());
            $result = new JsonResponse(['msg' => []], Response::HTTP_NOT_FOUND);
        } catch (HttpException $e) {
            $result = new JsonResponse([self::ERROR => $e->getMessage()], $e->getStatusCode());
        } catch (InvalidArgumentException $e){
            $this->logger->error('Uuid Not Valid:( -> ' . $e->getMessage());
            $result = new JsonResponse([self::ERROR=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (HasIp4sNetworkException $e){
            $this->logger->error('This Network has associated Ip4s :( -> ' . $e->getMessage());
            $result = new JsonResponse([self::ERROR=>$e->getMessage()], Response::HTTP_CONFLICT);
        }
        
        return $result;
    }
}