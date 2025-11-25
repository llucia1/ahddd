<?php
declare(strict_types=1);
namespace GridCP\Common\Domain\Const\Ip4;

class TypesOwnerOfIp4
{
    public const USER = 'User';
    public const GENUINE = 'Genuina';

    /**
     * Allowed values for owner_type
     * @return array
     */
    public static function getValues(): array
    {
        return [
            self::USER,
            self::GENUINE,
        ];
    }
}