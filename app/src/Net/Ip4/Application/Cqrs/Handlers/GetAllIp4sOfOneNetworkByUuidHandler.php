<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetAllIp4sOfOneNetworkByUuidQueried;
use GridCP\Net\Ip4\Application\Response\GetAllIpsOfOneNetworkExceptionResponse;
use GridCP\Net\Ip4\Application\Service\ListIp4ByNetworkUuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsMessageHandler]
final readonly class GetAllIp4sOfOneNetworkByUuidHandler implements QueryHandler
{
    public function __construct(private readonly ListIp4ByNetworkUuid $listIp4ByNetworkUuid)
    {
    }

    public function __invoke(GetAllIp4sOfOneNetworkByUuidQueried $networUuidQuery): null | Ip4sResponse | GetAllIpsOfOneNetworkExceptionResponse
    {
        try {
            $result = $this->listIp4ByNetworkUuid->getAllByNetworkUuid( new Ip4UuidNetwork( $networUuidQuery->uuid() ) );
            
        }catch(NerworkNoExistException $ex){
            $result = new GetAllIpsOfOneNetworkExceptionResponse( new NerworkNoExistException() );

        }catch(ListIp4EmptyException $ex){
            $result = new GetAllIpsOfOneNetworkExceptionResponse(new ListIp4EmptyException());

        }catch(\Exception $ex){
            $result = new GetAllIpsOfOneNetworkExceptionResponse(new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $ex->getMessage()));
        }
        return $result;
    }

}