<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Response;
use GridCP\Common\Domain\Bus\Query\Response;
final class Ip4SubnetsResponses implements Response
{
    private readonly  array $subnets;
    public function __construct(Ip4SubnetResponse ...$subnets)
    {
        $this->subnets = $subnets;
    }

    public function gets(): array
    {
        return $this->subnets;
    }
}