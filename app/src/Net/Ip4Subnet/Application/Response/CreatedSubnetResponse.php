<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Response;
use Exception;
use GridCP\Common\Domain\Bus\Query\Response;

class CreatedSubnetResponse implements Response
{
    public function __construct(
                                    private null | string | Exception $subnet
                                ){
                                }

    public function get(): null | string | Exception
    {
        return $this->subnet;
    }
}