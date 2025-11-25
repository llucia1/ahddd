<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\ValueObjects;

use InvalidArgumentException;

abstract class IntValueObject
{
    public function __construct(protected int $value)
    {
        $this->ensureIsValidInt($value);
    }

    public function ensureIsValidInt(int $value) {
        if (!is_int($value)) {
            throw new InvalidArgumentException("Value is not integer.");
        }
    }

    public function isBiggerThan(IntValueObject $other): bool
    {
        return $this->value() > $other->value();
    }

    public function value(): int
    {
        return $this->value;
    }

    public function isSmallerThan(IntValueObject $other): bool
    {
        return $this->value() < $other->value();
    }
}