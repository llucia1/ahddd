<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

class SubnetArrayResponse implements Response
{
    public function __construct(
                                    private ?array $subnet
                                ){
                                }

    public function get(): ?array
    {
        return $this->subnet;
    }
}