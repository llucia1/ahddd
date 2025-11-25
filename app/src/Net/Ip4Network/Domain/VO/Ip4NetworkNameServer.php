<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Domain\VO;

use GridCP\Common\Domain\ValueObjects\StringValueObject;

class Ip4NetworkNameServer extends StringValueObject
{
    private const MIN_LENGTH = 1;
    private const MAX_LENGTH = 15;

    public function __construct(protected string $value)
    {
        $this->ensureValidLength($value);
    }

    private function ensureValidLength(string $value): void
    {
        $length = strlen($value);
        
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The value must be a maximum %d characters long, got %d.',
                    self::MIN_LENGTH,
                    self::MAX_LENGTH,
                    $length
                )
            );
        }
    }

}