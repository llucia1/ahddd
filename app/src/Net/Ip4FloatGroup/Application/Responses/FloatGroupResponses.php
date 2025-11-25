<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Application\Responses;

final class FloatGroupResponses
{
    private readonly  array $floatGroups;
    public function __construct(FloatGroupResponse ...$floatGroup)
    {
        $this->floatGroups = $floatGroup;
    }

    public function gets(): array
    {
        return $this->floatGroups;
    }
}