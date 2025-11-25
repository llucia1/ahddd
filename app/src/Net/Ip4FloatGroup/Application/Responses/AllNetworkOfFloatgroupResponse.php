<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Application\Responses;
use GridCP\Common\Domain\Bus\Query\Response;


final class AllNetworkOfFloatgroupResponse  implements Response
{

    public function __construct( private array $nts){
        $this->nts = $nts;
    }

    public function get():?array
    {
        return $this->nts;
    }
}