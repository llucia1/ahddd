<?php

namespace GridCP\Net\Ip4Subnet\Domain\VO;

use GridCP\Common\Domain\ValueObjects\IntValueObject;
use InvalidArgumentException;

class SubnetMask extends IntValueObject
{
    private int  $valueMask;
    public function __construct( int $valueMask)
    {
        if ( is_int($valueMask)) {
            $this->setValueInt($valueMask);
        } else {
            parent::__construct($valueMask);
            $this->valueMask = $this->value();
        }
    }
    public function setValueInt(int $valueMask): void
    {
        if ($valueMask < 0 || $valueMask > 32) {
            throw new InvalidArgumentException(sprintf('Does not allow the value: %s', $valueMask));
        }
        $this->valueMask = $valueMask;
    }
    public function getValue(): int
    {
        return $this->valueMask;
    }

}