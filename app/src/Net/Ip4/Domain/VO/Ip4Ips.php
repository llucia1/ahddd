<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Domain\VO;

class Ip4Ips
{
    private array $ips;
    public function __construct(array $ipsAll)
    {
        foreach ($ipsAll as $ip) {
            $this->ips[] = new Ip4Ip($ip);
        }
    }

    public function get(): array
    {
        return $this->ips;
    }

}