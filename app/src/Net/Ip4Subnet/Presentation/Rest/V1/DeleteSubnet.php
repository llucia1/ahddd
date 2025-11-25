<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Presentation\Rest\V1;

use Exception;

use GridCP\Net\Ip4Subnet\Application\Service\DeleteSubnetService;
use GridCP\Net\Ip4Subnet\Domain\Exception\PropertySubnetNotFound;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetNoFound;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('api', name: 'api_v1_')]
final class DeleteSubnet extends AbstractController
{
    public function __construct(private readonly DeleteSubnetService $deleteSubnet, private readonly LoggerInterface $logger)
    {
    }

    #[Delete(// NOSONAR
        description: 'Delete a subnet by its uuid. - ROL ADMIN',// NOSONAR
        summary: 'Delete a Subnet',// NOSONAR
        security: [['Bearer' => []]],// NOSONAR
        tags: ['IP4 Subnet'],// NOSONAR
        responses: [// NOSONAR
            "204" => new OAResponse(// NOSONAR
                response: "204",// NOSONAR
                description: "Added",// NOSONAR
                content: new MediaType(// NOSONAR
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(// NOSONAR
                        type: "object",// NOSONAR
                        example: ['The Subnet has been deleted.'],// NOSONAR
                    ),
                ),
            ),
            "400" => new OAResponse(
                response: "400",// NOSONAR
                description: "Bad Request",// NOSONAR
                content: new MediaType(// NOSONAR
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(// NOSONAR
                        type: "object",// NOSONAR
                        example: [// NOSONAR
                            "error" => "Not Found",// NOSONAR
                        ],
                    ),
                ),
            ),
            "409" => new OAResponse(
                response: "409",// NOSONAR
                description: "Conflict",// NOSONAR
                content: new MediaType(// NOSONAR
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(// NOSONAR
                        type: "object",// NOSONAR
                        example: ["error" => "Not Found Input Data"],// NOSONAR
                    ),
                ),
            ),
            '500' => new OAResponse(// NOSONAR
                response: '500',// NOSONAR
                description: 'Internal Server Error',// NOSONAR
                content: new MediaType(// NOSONAR
                    mediaType: 'application/json',// NOSONAR
                    schema: new Schema(// NOSONAR
                        type: 'object',// NOSONAR
                        example: ['error' => 'Internal Server Error'],// NOSONAR
                    ),
                ),
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/v1/ip4/subnet/{uuid}', name: 'delete_subnet', methods: ['DELETE'])]// NOSONAR
    public function __invoke(string $uuid): JsonResponse// NOSONAR
    {
        
        try {
            $this->logger->info('Start Delete Subnet.');// NOSONAR
            $subnetUUid = new SubnetUuid($uuid);// NOSONAR
            $this->deleteSubnet->__invoke($subnetUUid);// NOSONAR
            return  $this->json(['status' => 'The Subnet has been deleted' ], Response::HTTP_NO_CONTENT);// NOSONAR
            
        } catch (SubnetNoFound $e) {// NOSONAR
            $this->logger->error("Not Found Subnet with uuid: " . $subnetUUid->value() . " :( ->" . $e->getMessage());// NOSONAR
            return $this->json(["error"=>$e->getMessage()], Response::HTTP_NOT_FOUND);// NOSONAR
        } catch (InvalidArgumentException $e){// NOSONAR
            $this->logger->error('Uuid Not Valid:( -> ' . $e->getMessage());// NOSONAR
            return  $this->json(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);// NOSONAR
        } catch (HttpException $e){// NOSONAR
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());// NOSONAR
        } catch (Exception $e) {// NOSONAR
            $this->logger->error("Error Delete Subnet", ["error" => $e->getMessage()]);// NOSONAR
            $httpCode = $e->getCode() > 0 && $e->getCode() < 600 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;// NOSONAR
            return  $this->json(['error' => $e->getMessage()], $httpCode);// NOSONAR
        }// NOSONAR
    }
}