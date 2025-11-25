<?php
declare(strict_types=1);

namespace GridCP\Common\Presentation\Rest\V2;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_v2_')]
class HealthCheck extends AbstractFOSRestController
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    #[Get(
        description: "Verify if the API is up and running. V2.",
        summary: "V2 Health Check",
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
                            "GridCP" => "Ok V2",
                        ],
                    )
                ),
            ),
        ],
    )]

    #[Route('/v2/healthcheck', name: 'healthcheck', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $this->logger->info("Request to HealthCheck");
        return $this->json(['GridCP' => 'Ok V2'], Response::HTTP_OK);
    }
}