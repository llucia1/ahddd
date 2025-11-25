<?php
declare(strict_types=1);
namespace Tests\Net\Ip4Subnet\Application\Helper;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;

trait MockerTrait
{
    protected static Client $client;

    private static function setUpMockHandler(array $responses): void
    {
        $mock = new MockHandler($responses);
        self::setUpClient($mock);
    }

    private static function setUpClient(MockHandler $mock): void
    {
        $handler = HandlerStack::create($mock);
        self::$client = new Client(['handler' => $handler, 'base_uri' => 'http://localhost:80']);
    }

    protected static function configMockHandler(int $codeResponse): void
    {
        self::setUpMockHandler([
            new Response($codeResponse)
        ]);
    }
}
