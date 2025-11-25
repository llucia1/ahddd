<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\ValueObjects;


use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Stringable;

class UuidValueObject implements Stringable
{

    public function __construct(protected string $value)
    {
        $this->ensureIsValidUuid($value);
    }

    private function ensureIsValidUuid(string $id): void
    {
        if (!RamseyUuid::isValid($id)) {
            throw new InvalidArgumentException(sprintf('Does not allow the value: %s', $id));
        }
    }

    public static function random(): self
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    public function equals(UuidValueObject $other): bool
    {
        return $this->value() === $other->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}