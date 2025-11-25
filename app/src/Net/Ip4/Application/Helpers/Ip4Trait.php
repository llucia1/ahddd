<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Helpers;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4Tag\Application\Response\Ip4TagResponse;

trait Ip4Trait
{

    private static function getOnlyActiveTag(Ip4Entity $ip4): ?Ip4TagResponse
    {
        $tag = $ip4->getTags()->first();
        if (!$tag) {
            return null;
        }

        return new Ip4TagResponse(
            $tag->uuid,
            $tag->tag,
            null
        );
    }
}