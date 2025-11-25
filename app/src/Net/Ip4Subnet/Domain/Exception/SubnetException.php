<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;

use GridCP\Common\Domain\Exceptions\NotAuthorizedByHeader;
use GridCP\Proxmox\Vm\Domain\Exception\IpNotFoundException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;



final class SubnetException {//NOSONAR

    public function __invoke(\exception $e):JsonResponse{
                return $this->handleException($e);
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
    
            case $e instanceof SubnetNoFound:
            case $e instanceof IpNotFoundException:
                $status = Response::HTTP_NOT_FOUND;
                $message = $e->getMessage();
                break;
    
            case $e instanceof NotAuthorizedByHeader:
            case $e instanceof FloatgroupNotValidException:
            case $e instanceof GetIpException:
            case $e instanceof IpsDuplicatedException:
            case $e instanceof GetFloatgroupException:
            case $e instanceof SubnetDuplicated:
            case $e instanceof GetUserException:
            case $e instanceof PropertySubnetNotFound:
            case $e instanceof NotValidTagIpException:
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
