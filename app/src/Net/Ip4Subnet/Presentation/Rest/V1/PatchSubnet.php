<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Presentation\Rest\V1;

use Exception;
use GridCP\Common\Domain\ValueObjects\Ip4Vo;
use GridCP\Common\Infrastructure\Jwt\UserServiceJwt;
use GridCP\Net\Ip4Subnet\Application\Request\CreateIpSubnetRequest;
use GridCP\Net\Ip4Subnet\Application\Request\PatchIpSubnetRequest;
use GridCP\Net\Ip4Subnet\Common\Service\PatchSubnetService;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetException;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\Ip4SubnetPacthVo;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetIP;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidFloatgroup;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api', name: 'api_v1_')]
final class PatchSubnet extends AbstractController
{
    public function __construct(
                                private readonly PatchSubnetService $patchSubnet,
                                private readonly UserServiceJwt $userServiceJwt,
                                private readonly LoggerInterface $logger
                                )
    {
    }

    #[Patch(
        description: 'Edit a Subnet with the provided data. - ROLE USER',
        summary: 'Edit a Subnet',
        security: [['Bearer' => []]],
        tags: ['IP4 Subnet'],
        responses: [
            "204" => new OAResponse(
                response: "204",
                description: "Edited",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: ['status' => 'Subnet edited.'],
                    ),
                ),
            ),
            "400" => new OAResponse(
                response: "400",
                description: "Unauthorized",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "Unauthorized",// NOSONAR
                        ],
                    ),
                ),
            ),
            "401" => new OAResponse(
                response: "401",
                description: "Bad Request",
                content: new MediaType(
                    mediaType: "application/json",// NOSONAR
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => "Bad Request",// NOSONAR
                        ],
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
                        example: ["error" => "Not Found Subnet"],
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
    #[Parameter(
        name: 'GridCPClient',
        description: "The client UUID for the Owner Subnet. If Client_uuid is null, ownership defaults to the organization's account .",
        in: 'header',
        required: false,
        schema: new Schema(
            type: 'string',
            example: '505bb1a7-dcd5-42bc-bda6-a6dde41e89d8'
        ),
    )]
    #[RequestBody(
        description: 'Provide the IP4 subnet data to edit IP4 subnet. - ROL USER',
        required: true,
        content: new MediaType(
            mediaType: 'application/json',
            schema: new Schema(
                ref: new Model(type: PatchIpSubnetRequest::class)
            )
        )
    )]

    #[IsGranted('ROLE_USER')]
    #[Route('/v1/ip4/subnet/{uuid}', name: 'pacth_subnet', methods: ['PATCH'])]
    public function __invoke(PatchIpSubnetRequest $request,string $uuid): JsonResponse
    {
        try {
            $this->logger->info('Start PACTH Subnet: ' . $uuid);

            $isAdmin = false;
            $subnetUUid = new SubnetUuid($uuid);
            $subnetUUidFloatGroup = null;
            $subnetMask = null;
            if($this->userServiceJwt->isAdmin()) {
                $subnetMask = (!is_null($request->getMask()))? new SubnetMask($request->getMask()) : null;
                $subnetUUidFloatGroup = (!is_null($request->getUuidFloatgroup()))? new UuidFloatgroup($request->getUuidFloatgroup()) : null;
                $isAdmin = true;
            }
            $subnetIP = (!is_null($request->getIp()))? new SubnetIP($request->getIp()) : null;

            $ip4Subnet = new Ip4SubnetPacthVo(
                $subnetUUid,  $subnetUUidFloatGroup,
                $subnetMask,  $subnetIP
            );

            $this->patchSubnet->__invoke($ip4Subnet, $isAdmin);
            return  $this->json(['status' => 'Subnet edited.' ], Response::HTTP_NO_CONTENT);
        } catch ( Exception $e){
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $subnetException = new SubnetException();
            return $subnetException($e);
        }
    }
}