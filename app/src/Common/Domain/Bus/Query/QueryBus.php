<?php
declare(strict_types=1);
namespace GridCP\Common\Domain\Bus\Query;

interface QueryBus
{
    public  function ask(Query $query):?Response;

}