<?php
declare(strict_types=1);

namespace GridCP\Common\Domain;

use DateTimeInterface;

final class Utils
{
    public static function toSnakeCase(string $text): string
    {
        return ctype_lower($text) ? $text : strtolower((string)preg_replace('/([^A-Z\s])([A-Z])/', "$1_$2", $text));
    }

    public static function toCamelCase(string $text): string
    {
        return lcfirst(str_replace('_', '', ucwords($text, '_')));
    }

    public static function dateToString(DateTimeInterface $date): string
    {
        return $date->format(DateTimeInterface::ATOM);
    }
}