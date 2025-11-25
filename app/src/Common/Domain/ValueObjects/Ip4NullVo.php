<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\ValueObjects;

use InvalidArgumentException;

class Ip4NullVo extends StringNullValueObject
{
    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->validateIp();
    }

    public function isSubnet(): bool
    {
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/[0-9]{1,3}$/', $this->value()) === 1;// NOSONAR
    }

    public function isSimpleIP(): bool
    {
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $this->value()) === 1;// NOSONAR
    }

    private function validateIp(): void
    {
        if (is_null($this->value())) {
            return;
        }
        if (!$this->isSubnet() && !$this->isSimpleIP()) {
            throw new InvalidArgumentException('Ip not valid: ' . $this->value());
        }
    }

}