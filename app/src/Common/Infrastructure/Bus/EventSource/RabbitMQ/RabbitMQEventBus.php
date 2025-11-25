<?php
declare(strict_types=1);
namespace GridCP\Common\Infrastructure\Bus\EventSource\RabbitMQ;

use GridCP\Common\Domain\Bus\EventSource\DomainEvent;
use GridCP\Common\Domain\Bus\EventSource\EventBus;
use GridCP\Common\Infrastructure\Bus\DomainEventJsonSerializer;
use function Lambdish\Phunctional\each;

final readonly class RabbitMQEventBus implements  EventBus
{

    public function __construct(
        private readonly RabbitMQConnection $connection,
        private string                      $exchangeName,
    )
    {

    }

    public function publish(DomainEvent ...$events): void
    {
        each($this->publisher(), $events);
    }

    public function publisher(): callable
    {
        return function (DomainEvent $event): void {
            try {
                $this->publishEvent($event);
            }catch (\AMQPException){

            }
        };
    }

    public function publishEvent(DomainEvent $event): void
    {
        $body = DomainEventJsonSerializer::serialize($event);
        $routingKey = $event::eventName();
        $messageId = $event->eventId();

        $this->connection->exchange($this->exchangeName)->publish(
            $body,
            $routingKey,
            AMQP_NOPARAM,
            [
                'message_id' =>$messageId,
                'content_type'=>'application/json',
                'content_encoding'=> 'utf-8'
            ]
        );
    }

}