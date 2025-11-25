<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\ValueObjects;

abstract class StringNullValueObject
{
    public function __construct(protected ?string $value)
    {
    }

    public function value(): ?string
    {
        return $this->value;
    }

}