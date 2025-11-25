<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Presentation\Rest\V1;

use Exception;
use GridCP\Net\Ip4Subnet\Domain\Exception\IpsDuplicatedException;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidFloatgroup;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidUser;
use GridCP\Net\Ip4Subnet\Application\Request\CreateIpSubnetRequest;
use GridCP\Net\Ip4Subnet\Application\Service\CreateIp4Subnet;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetException;
use GridCP\Net\Ip4Subnet\Domain\VO\Ip4SubnetVo;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetIP;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api', name: 'api_v1_')]
final class PostCreateIPSubnet extends AbstractController
{
    public function __construct(private readonly CreateIp4Subnet $createIp4Subnet, private readonly LoggerInterface $logger)
    {
    }

    #[Post(
        description: 'Create a new IP4 subnet with the provided data. - ROL ADMIN',
        summary: 'Create a new IP4 Subnet',
        security: [['Bearer' => []]],
        tags: ['IP4 Subnet'],
        responses: [
            '201' => new OAResponse(
                response: '201',
                description: 'Created',
                content: new MediaType(
                    mediaType: 'application/json',// NOSONAR
                    schema: new Schema(
                        type: 'object',
                        example: ['uuid' => 'd0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e'],
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
                        example: [
                            "error" => "Not Found",
                        ],
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
                        example: ["error" => "Not Found Input Data"],// NOSONAR
                    ),
                ),
            ),
            '500' => new OAResponse(
                response: '500',
                description: 'Internal Server Error',
                content: new MediaType(
                    mediaType: 'application/json',// NOSONAR
                    schema: new Schema(
                        type: 'object',
                        example: ['error' => 'Internal Server Error'],// NOSONAR
                    ),
                ),
            ),
        ],
    )]

    #[Parameter(// NOSONAR
        name: 'ip',
        description: 'The IP address.',
        in: 'query',
        required: false,
        schema: new Schema(
            type: 'string',
            format: 'ipv4',
            example: '192.168.0.1'
        ),
    )]

    #[Parameter(// NOSONAR
        name: 'mask',
        description: 'The subnet mask.',
        in: 'query',
        required: true,
        schema: new Schema(
            type: 'integer',
            example: 32
        ),
    )]

    #[Parameter(// NOSONAR
        name: 'uuidFloatgroup',
        description: 'The Floatgrouo Uuid associated with the subnet.',
        in: 'query',
        required: true,
        schema: new Schema(
            type: 'string',
            example: 'd0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e'
        ),
    )]

    #[RequestBody(// NOSONAR
        description: 'Provide the IP4 subnet data to create a new IP4 subnet.',
        required: true,
        content: new MediaType(
            mediaType: 'application/json',
            schema: new Schema(
                ref: new Model(type: CreateIpSubnetRequest::class)
            )
        )
    )]
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/v1/ip4/subnet', name: 'create_ip4_subnet', methods: ['POST'])]
    public function __invoke(CreateIpSubnetRequest $request): JsonResponse
    {
        try {
            
            $subnetUUid = new SubnetUuid(SubnetUuid::random()->value());
            $subnetUUidFloatGroup = new UuidFloatgroup($request->getUuidFloatgroup() );
            $subnetMask = new SubnetMask($request->getMask());
            $subnetIP = (!is_null($request->getIp()))? new SubnetIP($request->getIp()) : null;
            $this->logger->info('Start creating IP4 Subnet: ' . (($subnetIP) ? $subnetIP->value() : ''));

            $ip4Subnet = new Ip4SubnetVo(
                $subnetUUid,  $subnetUUidFloatGroup,
                $subnetMask,  $subnetIP
            );

            $uuid = $this->createIp4Subnet->__invoke($ip4Subnet);

            return $this->json(['uuid' => $uuid], Response::HTTP_CREATED);

        } catch ( Exception $e){
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $propertySubnetException = new SubnetException();
            return $propertySubnetException($e);
        }




    }
}
