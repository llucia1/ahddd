<?php
declare(strict_types=1);

namespace GridCP\Common\Domain\Bus\EventSource;

interface EventBus
{
    public function publish(DomainEvent ...$events): void;

}