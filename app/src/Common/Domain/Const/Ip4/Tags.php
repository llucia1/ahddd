<?php
declare(strict_types=1);
namespace GridCP\Common\Domain\Const\Ip4;

class Tags {
    public const RESERVED = 'reserved';
    public const SUSPENDED = 'suspended';
    public const BLACKLIST = 'blacklist';
    public const WHITELIST = 'whitelist';
    public const QUARANTINE = 'quarantine';
    public const INPROGRESS = 'inProgress';


    
    public static function isValidTag(?string $tag): bool
    {
        $valid = false;
        $validTags = [
            self::RESERVED,
            self::SUSPENDED,
            self::BLACKLIST,
            self::WHITELIST,
            self::QUARANTINE,
            self::INPROGRESS
        ];
        if ( is_null($tag) || $tag === self::BLACKLIST) {
                $valid = true;
        }
        return $valid;
    }
}