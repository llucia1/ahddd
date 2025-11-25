<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;

use GridCP\Net\Ip4\Application\Response\GetAllIpsOfOneNetworkExceptionResponse;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetFreeResponse;

final class GetIpsOfOneNetworkByUuidException
{
    public function __invoke(\Exception $ex):JsonResponse{
       return  $this->handleException($ex);
    }


    protected function handleException(\Exception $e): JsonResponse
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Unexpected error occurred.';
        
        switch (true) {
            case $e instanceof InvalidArgumentException:
                $status = Response::HTTP_BAD_REQUEST;
                $message = $e->getMessage();
                break;
    
            case $e instanceof NetworknotExistException:
            case $e instanceof SubnetsNoFound:
                $status = Response::HTTP_NOT_FOUND;
                $message = $e->getMessage();
                break;
    
            case $e instanceof GetIpsEmptyOfOneNetworkException:
            case $e instanceof GetIpsOfOneNetworkException:
            case $e instanceof ListIp4NetworkEmptyException:
            case $e instanceof SubnetFreeResponse:
            case $e instanceof GetAllIpsOfOneNetworkExceptionResponse:
                $status = Response::HTTP_CONFLICT;
                $message = $e->getMessage();
                break;
    
            case $e instanceof HttpException:
                $status = $e->getStatusCode();
                $message = $e->getMessage();
                break;
    
            default:
                break;
        }
    
        return new JsonResponse(['error' => $message], $status);
    }
}