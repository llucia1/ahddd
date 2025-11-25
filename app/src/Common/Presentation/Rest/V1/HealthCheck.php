<?php
declare(strict_types=1);

namespace GridCP\Common\Presentation\Rest\V1;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Response as OAResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_v1_')]
class HealthCheck extends AbstractFOSRestController
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    #[Get(
        description: "Verify if the API is up and running. V1.",
        summary: "V1 Health Check",
        tags: ["Health Check"],
        responses: [
            "200" => new OAResponse(
                response: "200",
                description: "Success",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                            "GridCP" => "Ok",
                        ],
                    )
                ),
            ),
        ],
    )]

    #[Route('/v1/healthcheck', name: 'healthcheck', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $this->logger->info("Request to HealthCheck");
        return $this->json(['GridCP' => 'Ok'], Response::HTTP_OK);
    }
}