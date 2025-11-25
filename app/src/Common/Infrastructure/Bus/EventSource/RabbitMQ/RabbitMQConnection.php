<?php
declare(strict_types=1);
namespace GridCP\Common\Infrastructure\Bus\EventSource\RabbitMQ;

use AMQPConnection;
use AMQPChannel;
use AMQPExchange;
use AMQPQueue;

final class RabbitMQConnection
{

    private static ?AMQPConnection $connection = null;
    private static ?AMQPChannel $channel = null;

    /** @var AMQPExchange[] */
    private static  array $exchanges = [];

    /** @var AMQPQueue[] */
    private static array $queues = [];

    public function  __construct(private readonly array $configuration){
        var_dump("ENTRA EN CLASE RABBITMQ CONNECTION");
    }

    public function __invoke():void{
        $this->connection();
    }
    public function queue(string $name): AMQPQueue
    {
        if (!array_key_exists($name, self::$queues)){
            $queue = new AMQPQueue($this->channel());
            $queue->setName($name);
            self::$queues[$name]=$queue;

        }

        return self::$queues[$name];
    }

    private function channel():AMQPChannel
    {
        if (!self::$channel?->isConnected()){
            self::$channel = new AMQPChannel($this->connection());
        }
        return self::$channel;
    }

    private function connection():AMQPConnection
    {
        var_dump("Entra en configuración");
        if(self::$connection === null){
            self::$connection = new AMQPConnection($this->configuration);
        }
        if(!self::$connection->isConnected()){
            self::$connection->pconnect();
        }
        return self::$connection;
    }

    public function exchange(string $name): AMQPExchange
    {
        if(!array_key_exists($name, self::$exchanges)){
            $exchange = new AMQPExchange($this->channel());
            $exchange->setName($name);
            self::$exchanges[$name]= $exchange;
        }

        return self::$exchanges[$name];
    }

}