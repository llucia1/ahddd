<?php
declare(strict_types=1);
namespace GridCP\Device\Application\Cqrs\Handlers;

use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Device\Application\Cqrs\Queries\SearchDeviceActiveByAuthUserQuerie;
use GridCP\Device\Application\Response\IpResponse;
use GridCP\Device\Domain\Repository\IDeviceAuthRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly  class SearchDeviceActiveByAuthUserQuerieHandler implements QueryHandler
{

    public function __construct(private readonly IDeviceAuthRepository $deviceAuthRepository, private readonly LoggerInterface $logger){

    }
    public function __invoke(SearchDeviceActiveByAuthUserQuerie $query):IpResponse{
       return  $this->getDeviceActiveByAuthUser($query->get());
    }

    private function getDeviceActiveByAuthUser(int $userId):IpResponse{
        try {
            $this->logger->info("Searching device active by auth user id: $userId");
            $ip = $this->deviceAuthRepository->findDeviceByAuthId($userId);
            if(!empty($ip)){
                return new IpResponse($ip[0]['ip']);
            }else{
                return new IpResponse("");
            }


        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}