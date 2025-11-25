<?php
declare(strict_types=1);
namespace GridCP\Common\Domain\ValueObjects;

use InvalidArgumentException;

class PortVo extends IntValueObject
{

    private const MIN_PORT = 1;
    private const MAX_PORT = 65535;

    public function __construct(string|int $value)
    {
        $this->ensureValidNumeric($value);

        parent::__construct((int) $value);
        $this->ensureValidPort($value);
    }

    private function ensureValidPort(int $port): void
    {
        if ($port < self::MIN_PORT || $port > self::MAX_PORT) {
            throw new InvalidArgumentException("The port number must be between " . self::MIN_PORT . " and " . self::MAX_PORT . ". Given: " . $port);
        }
    }
    private function ensureValidNumeric(string|int $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException("The port number must be a valid integer. Given: " . var_export($value, true));
        }
    }
}