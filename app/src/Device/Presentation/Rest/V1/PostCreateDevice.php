<?php
declare(strict_types=1);

namespace GridCP\Device\Presentation\Rest\V1;

use GridCP\Common\Infrastructure\Jwt\UserServiceJwt;
use GridCP\Device\Application\Request\CreateDeviceRequest;
use GridCP\Device\Application\Service\CreateDeviceService;
use GridCP\Device\Application\Service\CreateDeviceAuthService;
use GridCP\Device\Domain\VO\Device;
use GridCP\Device\Domain\VO\DeviceCountry;
use GridCP\Device\Domain\VO\DeviceDevice;
use GridCP\Device\Domain\VO\DeviceIp;
use GridCP\Device\Domain\VO\DeviceLocation;
use GridCP\Device\Domain\VO\DeviceUuid;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_v1_')]
final class PostCreateDevice extends AbstractController
{
    public function __construct(
        private readonly CreateDeviceService $createDeviceService,
        private readonly CreateDeviceAuthService $createDeviceAuthService,
        

        private readonly LoggerInterface  $logger,
        private readonly UserServiceJwt $userServiceJwt
    )
    {
    }

    #[Post(
        description: "Register new Device with the provided data.",
        summary: "Register new Device",
        tags: ["Device"],
        responses: [
            "201" => new OAResponse(
                response: "201",
                description: "Created",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["uuid" => "d0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e"],
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
                        example: ["error" => "Bad Request"],
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
                        example: ["error" => "Unauthorized"],
                    ),
                ),
            ),
            "403" => new OAResponse(
                response: "403",
                description: "Forbidden",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Forbidden"],
                    ),
                ),
            ),
            "404" => new OAResponse(
                response: "404",
                description: "Not Found",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: ["error" => "Not Found"],
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
        name: "ip",
        description: "The IP address.",
        in: "query",
        required: true,
        example: "192.168.1.2",
    )]

    #[Parameter(
        name: "device",
        description: "The device.",
        in: "query",
        required: true,
        example: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
    )]

    #[Parameter(
        name: "country",
        description: "The country.",
        in: "query",
        required: true,
        example: "ES",
    )]

    #[Parameter(
        name: "location",
        description: "The location.",
        in: "query",
        required: false,
        example: "MAD",
    )]
    

    #[RequestBody(
        description: "Provide the Device data to create a new device.",
        required: true,
        content: new MediaType(
            mediaType: "application/json",
            schema: new Schema(
                ref: new Model(type: CreateDeviceRequest::class)
            )
        ),
    )]

    #[Route('/v1/device', name: 'create_device', methods: ['POST'])]
    public function __invoke(CreateDeviceRequest $request): JsonResponse
    {
        
        try {
            $this->logger->info('Start create new device' . $request->device());
            $deviceUuid = new DeviceUuid( DeviceUuid::random()->value() );
            $deviceDevice = new DeviceDevice( $request->device() );
            $deviceIp = new DeviceIp( $request->ip());
            $deviceCountry = new DeviceCountry( $request->country() );
            $deviceLocation = new DeviceLocation( $request->location() );

            $device = Device::create(
                                        $deviceUuid,
                                        $deviceIp,
                                        $deviceDevice,
                                        $deviceCountry,
                                        $deviceLocation
                                    );
            $uuid = $this->createDeviceService->__invoke($device);
            if($uuid) {
                $jwtData = $this->userServiceJwt->getCurrentUser();
                $this->logger->info('Start create new Auth Device' . $request->device());
                $this->createDeviceAuthService->__invoke($uuid, $jwtData);
            }
            return new JsonResponse(data: ['uuid' => $uuid], status: Response::HTTP_CREATED);
        } catch (HttpException $e){
            $this->logger->error('Error in create new Device :( -> ' . $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
        }





    }
}