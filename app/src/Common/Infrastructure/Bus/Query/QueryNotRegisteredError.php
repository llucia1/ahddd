<?php
declare(strict_types=1);
namespace GridCP\Common\Infrastructure\Bus\Query;



use GridCP\Common\Domain\Bus\Query\Query;

final class QueryNotRegisteredError extends \RuntimeException
{
    public function  __construct(Query $query)
    {
        $queryClass = $query::class;
        parent::__construct("The query <$queryClass> has no associated query Handler :(");
    }

}