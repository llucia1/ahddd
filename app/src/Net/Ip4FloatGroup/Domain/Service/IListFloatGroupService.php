<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Domain\Service;


use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponses;

interface IListFloatGroupService
{
    function getAll():FloatGroupResponses;
    function toResponse():callable;
}