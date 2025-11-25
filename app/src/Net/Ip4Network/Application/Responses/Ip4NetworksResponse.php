<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Application\Responses;

final class Ip4NetworksResponse
{
    private readonly  array $ip4_networks;
    public function __construct(Ip4NetworkResponse ...$ip4_networks)
    {
        $this->ip4_networks = $ip4_networks;
    }

    public function ip4_networks(): array
    {
        return $this->ip4_networks;
    }
}