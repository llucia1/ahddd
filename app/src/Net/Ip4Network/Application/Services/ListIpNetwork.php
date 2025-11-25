<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Application\Services;

use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkResponse;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworksResponse;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Ip4Network\Domain\Model\Ip4NetworkModel;
use GridCP\Net\Ip4Network\Domain\Repository\IIp4NetworkRepository;
use GridCP\Net\Ip4Network\Domain\Services\IListIp4NetworkService;
use function Lambdish\Phunctional\map;

readonly class ListIpNetwork implements IListIp4NetworkService
{

    public function __construct(private IIp4NetworkRepository $ip4NetworkRepository){}


    public function __invoke():Ip4NetworksResponse
     {
      return $this->getAll();

    }
    public function getAll():Ip4NetworksResponse
    {
        $ipNetworks =  $this->ip4NetworkRepository->getAll();
        return  (is_null($ipNetworks))
            ?throw new ListIp4NetworkEmptyException()
            :new Ip4NetworksResponse( ...map($this->toResponse() , $ipNetworks));
    }
    public function toResponse():callable
    {
        return static fn (Ip4NetworkModel $ip4Network): Ip4NetworkResponse=> new Ip4NetworkResponse(
            $ip4Network->getUuid(),
            $ip4Network->getName(),
            $ip4Network->getNameServer1(),
            $ip4Network->getNameServer2(),
            $ip4Network->getNameServer3(),
            $ip4Network->getNameServer4(),
            $ip4Network->getPriority(),
            $ip4Network->getNetmask(),
            $ip4Network->getGateway(),
            $ip4Network->getBroadcast(),
            $ip4Network->getFloatGroup()
        );
    }
}
