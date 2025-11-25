<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Application\Responses;




use GridCP\Common\Domain\Bus\Query\Response;

final readonly class Ip4NetworkQueryResponse implements Response
{
    public function __construct(private ?Ip4NetworkResponse $Ip4NetworkResponse)
    {}
 
    public function get():?Ip4NetworkResponse
    {
        return $this->Ip4NetworkResponse;
    }
}