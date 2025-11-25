<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;


final class SubnetsException// NOSONAR
{
    public function __invoke(\Exception $exception)
    {
        return $this->handleException($exception);
    }

    protected function handleException(\Exception $e)
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Unexpected error occurred.';
    
        switch (true) {
            case $e instanceof InvalidArgumentException:
                $status = Response::HTTP_BAD_REQUEST;
                $message = $e->getMessage();
                break;
    
            case $e instanceof SubnetNoFound:
            case $e instanceof \GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsNoFound:
                $status= Response::HTTP_NO_CONTENT;
                $message = $e->getMessage();
                break;
            case $e instanceof \GridCP\Net\Ip4Subnet\Domain\Exception\PropertySubnetNotFound:
            case $e instanceof ClientNoFound:
                $status = Response::HTTP_NOT_FOUND;
                $message = $e->getMessage();
                break;
    
            case $e instanceof SubnetDuplicated:
            case $e instanceof GetClientException:
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