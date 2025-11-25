<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Presentation\Rest\V1;

use GridCP\Net\Ip4FloatGroup\Application\Service\FloatGroupByUUIDService;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ListFloatGroupEmptyException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
#[Route('/api', name: 'api_v1_')]
class GetFloatGroupByUUID extends AbstractController
{
    public function __construct(
        private readonly FloatGroupByUUIDService $floatGroupByUUIDService,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Get(
        description: "Get an Float Group with the given UUID.",
        summary: "Get an Float Group",
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
                            "uuid" => "a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6",
                            "name" => "Madrid",
                            "ipNetworks" => [
                                                [
                                                    "uuid" => "5454545454-df4545454d-f5454df-454",
                                                    "name" => "prueba",
                                                ],
                                                [
                                                    "uuid" => "5454545454-df4545454d-f5454df-454",
                                                    "name" => "prueba2",
                                                ]
                                            ],
                                "node" => [
                                                [
                                                  "uuid" => "f48f8880-71c6-44af-ae24-e73f791acd3c",
                                                  "gcp_node_name" => "individual-1",
                                                  "pve_node_name" => "ns1047",
                                                  "priority" => 9,
                                                  "recommended" => true
                                                ],
                                                [
                                                  "uuid" => "4327eb71-6a8d-4e89-8d51-b955ec5a8632",
                                                  "gcp_node_name" => "node188",
                                                  "pve_node_name" => "node1",
                                                  "priority" => 8,
                                                  "recommended" => false
                                                ],
                                                [
                                                  "uuid" => "7e561da4-5e55-43ea-aef2-43e3e2ef7b7c",
                                                  "gcp_node_name" => "individual-2",
                                                  "pve_node_name" => "ns1048",
                                                  "priority" => 8,
                                                  "recommended" => true
                                                ]
                                              ],
                        ],
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "404",
                description: "Float Group does not exist",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["'error': 'Not Found Float Group'"],
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
                        example: ["error" => "Internal Server Error"],
                    ),
                ),
            ),
        ],
    )]

    #[Parameter(
        name: "uuid",
        description: "The UUID of the Float Group.",
        in: "path",
        required: true,
        example: "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e",
    )]
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/float_group/{uuid}', name: 'get_floatgroup_by_uuid', methods: ['GET'])]
    public function __invoke(string $uuid): JsonResponse
    {
        try {
            $this->logger->info('Get Float Group with UUID: ' . $uuid);
            $floatGroup = $this->floatGroupByUUIDService->__invoke($uuid);
            return $this->json(
                [
                   'uuid' => $floatGroup->uuid(),
                   'name' => $floatGroup->name(),
                   'ipNetworks' => $floatGroup->networks(),
                   'node' => $floatGroup->floatgroup()
                ],
                Response::HTTP_OK
            );
        }catch (ListFloatGroupEmptyException $e){
            $this->logger->error($e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (HttpException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}