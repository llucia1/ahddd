<?php

namespace GridCP\Net\Ip4FloatGroup\Domain\VO;

use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ErrorUuidInvalid;

class Ip4FloatGroupsUuid extends UuidValueObject
{
    public function __construct(protected string $value)
    {
        
        try {
            parent::__construct($value);
        } catch (\InvalidArgumentException $e) {
            throw new ErrorUuidInvalid($value);
        }

    }
}