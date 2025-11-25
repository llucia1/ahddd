<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Domain\VO;

use GridCP\Common\Domain\ValueObjects\StringValueObject;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NotValid;

class Ip4Ip extends StringValueObject
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
        if (!$this->isSubnet() && !$this->isSimpleIP()) {
            throw new Ip4NotValid($this->value());
        }
    }

}