<?php


namespace GridCP\Common\Domain\ValueObjects;

use GridCP\Common\Domain\ValueObjects\StringValueObject;

use GridCP\Common\Domain\Const\Ip4\Tags;
use GridCP\Common\Domain\Const\NotSet;

class Ip4TagTag
{

    public function __construct(protected ?string $value)
    {
        $this->ensureValid($value);
        $this->value = $value;
        $this->ensureNotSet($value);
    }

    public function value(): ?string
    {
        return $this->value;
    }




    private function ensureValid(string $value): void
    {
        if ( $value === NotSet::VALUE ) {
            return;
        }
        $validTags = [
            Tags::RESERVED,
            Tags::SUSPENDED,
            Tags::BLACKLIST,
            Tags::WHITELIST,
            Tags::QUARANTINE,
            Tags::INPROGRESS,
        ];

        if (!in_array($value, $validTags, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid tag value: "%s"', $value));
        }
    }

    private function ensureNotSet(?string $value): void
    {
        if ( $value === NotSet::VALUE ) {
            $this->value = null;
        }
    }
}