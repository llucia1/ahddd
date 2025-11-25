<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Tag\Domain\Exception;

use GridCP\Common\Domain\Exceptions\OpenSSLEncryptError;
use GridCP\Proxmox\Vm\Domain\Exception\IpNotFoundException;
use GridCP\Proxmox\Vm\Domain\Exception\ListNodesEmptyError;
use GridCP\Proxmox\Vm\Domain\Exception\OsNameNotExistException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;



final class Ip4TagsExceptions {

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
    
            case $e instanceof ErrorIpNotExists:
                $status = Response::HTTP_NOT_FOUND;
                $message = $e->getMessage();
                break;            



            case $e instanceof IpNotFoundException:
            case $e instanceof Ip4NotExits:
            case $e instanceof CreateIp4TagException:
            case $e instanceof Ip4TagDuplicated:
            case $e instanceof OsNameNotExistException:
            case $e instanceof OpenSSLEncryptError:
            case $e instanceof Ip4TagInsertErrorDBException:
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
