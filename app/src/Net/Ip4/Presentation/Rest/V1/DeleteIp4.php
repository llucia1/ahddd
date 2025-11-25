<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Presentation\Rest\V1;

use GridCP\Net\Ip4\Application\IP4Request;
use GridCP\Net\Ip4\Application\Service\DeleteIP4 as ServiceDeleteIP4;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4AreAsignnedException;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4GenuineNotValidDelete;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4InSubnetNotValidDelete;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NotFound;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NotValid;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NotValidDelete;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4sInSubnetsException;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\VO\Ip4;
use GridCP\Net\Ip4\Domain\VO\Ip4Ip;
use GridCP\Net\Ip4\Domain\VO\Ip4Ips;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\RequestBody;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Nelmio\ApiDocBundle\Annotation\Model;

use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/api', name: 'api_v1_')]
class DeleteIp4 extends AbstractController
{
    public function __construct(
                                    private readonly ServiceDeleteIP4 $deleteIp4, 
                                    private readonly LoggerInterface $logger
                                )
                                
    {
    }

    #[Delete(
        description: "Delete IP4.",
        summary: "Delete IP4",
        security: [["Bearer" => []]],
        tags: ["IP4"],
        responses: [
            "204" => new OAResponse(
                response: "204",
                description: "Deleted",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                                     "status" => "Ip4's has been deleted"
                                ]
                    ),
                )
            ),
            "404" => new OAResponse(
                response: "404",
                description: "Not Found",
                content: new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: "object",
                        example: [
                            "error" => ['192.0.1.2', '192.0.1.8'],
                        ],
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
                            "error" => "IP4 not Valid",
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
                        example: ["{'error':'Nodes not exists'}"],
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
                            "error" => "An error occurred while fetching IP4s",
                        ],
                    ),
                ),
            ),
        ],
    )]


    #[Parameter(
        name: "ips",
        description: "IPs address.",
        in: "query",
        required: true,
        schema: new Schema(
            type: "string",
            example: '192.1.8.88'
        ),
    )]

    
    #[RequestBody(
        description: "DELETE IP4s.",
        required: true,
        content: new MediaType(
            mediaType: "application/json",
            schema: new Schema(
                ref: new Model(type: IP4Request::class),
                schema: "IP4Request",
                title: "IP4 Request",
                description: "Request schema for IP4.",
                required: ["ip"],
                type: "object"
            )
        ),
    )]    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/v1/ip4', name: 'delete_ip4', methods: ['DELETE'])]
    public function __invoke(IP4Request $request): JsonResponse
    {
        
        try {
            $ip4s = $request->getIPs();
            $this->logger->info('Start: Deleting All IP4.');
            $ip4sVo = new Ip4Ips($ip4s);
            

            $notFound = $this->deleteIp4->__invoke($ip4sVo);

            if ($notFound && !empty($notFound)) {
                throw new Ip4NotFound('');
            }

            return new JsonResponse(['status' => "Ip4's has been deleted"], Response::HTTP_NO_CONTENT);

        } catch (Ip4NotFound $e) {
            return new JsonResponse(['error' => $notFound], Response::HTTP_NOT_FOUND);
        } catch (Ip4GenuineNotValidDelete $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (Ip4InSubnetNotValidDelete $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }   catch (Ip4AreAsignnedException $e) {
            $this->logger->error('The following Ips are assigned (Genuine Ip). Cannot be changed: ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        }  catch (Ip4sInSubnetsException $e) {
            $this->logger->error('The following Ips are a one Subnet. Cannot be changed: ' . $e->getMessage());
            return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
        }    catch (Ip4NotValid $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (HttpException $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function toTxt( array $data): string
    {
        $txt = '';
        foreach ($data as $txt){
            (is_array($txt)) ? $txt .= $this->toTxt($txt) : $txt .= $txt;
        }
        return $txt;
    }

    
}