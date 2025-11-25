<?php
namespace GridCP\Net\Ip4Subnet\Domain\VO;


use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use InvalidArgumentException;

class UuidUser extends UuidValueObject
{
    private int | string $valueUuid;
    public function __construct( int | string $uuid)
    {
        if ( is_int($uuid)) {
            $this->setValueInt($uuid);
        } else {
            parent::__construct($uuid);
            $this->valueUuid = $this->value();
        }
    }
    public function setValueInt(int $valueInt): void
    {
        if ($valueInt !== 0) {
            throw new InvalidArgumentException(sprintf('Does not allow the value: %s', $valueInt));
        }
        $this->valueUuid = $valueInt;
    }
    public function getValueUuid(): int | string
    {
        return $this->valueUuid;
    }
}