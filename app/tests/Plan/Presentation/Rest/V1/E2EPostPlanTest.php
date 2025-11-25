<?php
declare(strict_types=1);

namespace Tests\Plan\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;


class E2EPostPlanTest extends WebTestCase
{
    protected static Client $client;
    protected static $token;
    protected static array $data;
    protected static $uuid = '1b52504c-78a3-4743-8e7e-ccdc0181f03f';
    public static function  setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();
        self::$data = [

            "name" =>  "LC-2G",
            "disk_size" => 10,
            "cores" => 1,
            "memory" =>  2048,
            "traffic_limit" =>  8000,

        ];

        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK),
            new Response(ResponseCode::HTTP_NO_CONTENT)
        ]);

        $handlerStack = HandlerStack::create($mock);
        self::$client = new Client(['base_uri' => 'http://localhost:80']);


        $response = self::$client->post('/api/v1/auth/login', [
            'json' => [
                'email' => 'xavi@xavi.com',
                'password' => 'password'
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];
    }

    public  static function testCreatePlanWhenAllDataIsCorrect():void{
        $response = self::$client->post('/api/v1/plan', [
                                                                            'http_errors' => false, 
                                                                            'headers' => [
                                                                                'Authorization' => 'Bearer ' . self::$token
                                                                            ],
                                                                            'json'=>self::$data
                                                                        ]);
        self::assertEquals(ResponseCode::HTTP_CREATED, $response->getStatusCode());
    }
    // php bin/phpunit tests/Plan/Presentation/Rest/V1/E2EPostPlanTest.php
}
