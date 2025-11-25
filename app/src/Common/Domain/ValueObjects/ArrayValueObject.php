<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\ValueObjects;

abstract class ArrayValueObject
{
    public function __construct(protected array $value)
    {
    }

    public function value(): array
    {
        return $this->value;
    }

}