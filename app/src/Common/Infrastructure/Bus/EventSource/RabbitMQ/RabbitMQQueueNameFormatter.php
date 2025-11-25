<?php
declare(strict_types=1);
namespace GridCP\Common\Infrastructure\Bus\EventSource\RabbitMQ;

use GridCP\Common\Domain\Bus\EventSource\DomainEventSubscriber;
use GridCP\Common\Domain\Utils;
use function Lambdish\Phunctional\last;
use function Lambdish\Phunctional\map;

final class RabbitMQQueueNameFormatter
{
    public static function format(DomainEventSubscriber $subscriber): string
    {
        $subscriberClassPaths = explode('\\', str_replace('GridCP', 'gridcp',$subscriber::class));
        $queueNameParts = [
            $subscriberClassPaths[0],
            $subscriberClassPaths[1],
            $subscriberClassPaths[2],
            last($subscriberClassPaths)
        ];

        return implode('.', map(self::toSnakeCase(), $queueNameParts));
    }


    public static function formatRetry(DomainEventSubscriber $subscriber): string
    {
        $queueName= self::format($subscriber);
        return "retry.$queueName";
    }

    public static function formatDeadLetter(DomainEventSubscriber $subscriber):string
    {
        $queueName = self::format($subscriber);
        return "dead_letter.$queueName";
    }

    public function shortFormat(DomainEventSubscriber $subscriber):string
    {
        $subscriberCamelCaseName= (string) last(explode('\\', $subscriber::class));
        return Utils::toSnakeCase($subscriberCamelCaseName);
    }

    private static function toSnakeCase():callable
    {
        return static fn(string $text): string => Utils::toSnakeCase($text);
    }
}