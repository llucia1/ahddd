<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Presentation\Rest\V1;

use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;
use GridCP\Net\Ip4FloatGroup\Application\Service\ListFloatGroup;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ListFloatGroupEmptyException;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Response as OAResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use function Lambdish\Phunctional\map;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/api', name: 'api_v1_')]
final class GetAllFloatGroup extends AbstractController
{
    public function __construct(private readonly ListFloatGroup $listFloatGroup, private readonly LoggerInterface $logger)
    {
    }

    #[Get(
        description: "Get all Float Groups.",
        summary: "Get all Float Groups",
        security: [["Bearer" => []]],
        tags: ["Float Group"],
        responses: [
            "200" => new OAResponse(
                response: "200",
                description: "Success",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                              "uuid" => "4f79ad5e-8922-4fd7-a09b-e1eb5ec5b215",
                              "name" => "Ubrique",
                              "active" => true,
                              "ipNetworks" => [
                                                [
                                                  "uuid" => "c9442ba1-bef7-41c2-b9dc-360e19f09ad0",
                                                  "name" => "192.168.0.1"
                                                ],
                                                [
                                                  "uuid" => "f01ae18f-edf0-4da1-b9e2-81b1a48091de",
                                                  "name" => "192.168.28.1"
                                                ]
                                              ],                
                              "node" => [
                                                [
                                                  "uuid" => "f48f8880-71c6-44af-ae24-e73f791acd3c",
                                                  "name" => "individual-1"
                                                ],
                                                [
                                                  "uuid" => "f48f8880-71c6-44af-ae24-e73f791acd3c",
                                                  "name" => "individual-1"
                                                ],
                                        ],
                                ],
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "204",
                description: "Not Content",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "There are no Float Groups",
                        ],
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
                        example: [
                            "error" => "An error occurred while fetching Float Groups",
                        ],
                    ),
                ),
            ),
        ],
    )]
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/float_group', name: 'get_float_group', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        try {
            $this->logger->info('Get All Float Groups');
            $floatGroups = $this->listFloatGroup->__invoke();
            return $this->json(
                map(
                    fn(FloatGroupResponse $floatGroup): array => [
                        'uuid' => $floatGroup->uuid(),
                        'name' => $floatGroup->name(),
                        'ipNetworks' => $floatGroup->networks(),
                        'node' => $floatGroup->floatgroup()
                    ],
                    $floatGroups->gets()
                ),
                Response::HTTP_OK
            );
        }catch (ListFloatGroupEmptyException $e){
            $this->logger->error($e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (HttpException $e) {
            $this->logger->error($e->getMessage());
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        }

    }
}