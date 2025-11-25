<?php
declare(strict_types=1);

namespace Device\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EPostDeviceTest extends WebTestCase
{
    protected static array $data;
    protected static $client;
    protected static $uuid = '1b52504c-78a3-4743-8e7e-ccdc0181f03f';
    public static function  setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();
        self::$data = [
            "ip" => $faker->ipv4(),
            "device" => $faker->userAgent(),
            "country" => $faker->countryCode(),
            "location" => $faker->city()
        ];
        self::$client = new Client(['base_uri' => 'http://localhost:80']);
    }

    public  static function testCreateDeviceWhenAllDataIsCorrect():void{
        $response = self::$client->post('/api/v1/device', ['json'=>self::$data]);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public  static function testCreateDeviceWhenNotBodySend():void{
        $response = self::$client->request('POST', '/api/v1/device',['http_errors'=>false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateDeviceWhenExist():void{
        $response = self::$client->request('POST', '/api/v1/device',['http_errors'=>false,['body'=>self::$data]]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    
}