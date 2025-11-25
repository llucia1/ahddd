<?php
declare(strict_types=1);
namespace GridCP\Device\Application\Cqrs\Queries;

use GridCP\Common\Domain\Bus\Query\Query;

final class SearchDeviceActiveByAuthUserQuerie implements Query
{
    public function __construct(private ?int $id){

    }

    public function get():?int{
        return $this->id;
    }

}