<?php
declare(strict_types=1);

namespace Proxmox\Common\Timezones\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EGetTimezonesTest extends WebTestCase
{
    protected static Client $client;
    protected static array $USER_LOGIN;
    protected static array $CORRECT_REQUEST_DATA;

    protected static array $INCORRECT_REQUEST_DATA;

    protected static array $PARAMETER_INCORRECT_REQUEST_DATA;

    protected static string $JWT;

    public static function setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();

        self::$USER_LOGIN = [
            "email" => "user2@email.es",
            "password" => "user2"
        ];

        self::$CORRECT_REQUEST_DATA = [
            "name" => $faker->name(),
        ];

        self::$INCORRECT_REQUEST_DATA = [
            "name" => null
        ];

        self::$PARAMETER_INCORRECT_REQUEST_DATA = [
            "name" => $faker->randomNumber(1),
        ];

        self::login();
        self::$client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.self::$JWT]]);
    }

    public static function login():void
    {
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>self::$USER_LOGIN]);
        self::$JWT =   json_decode($result->getBody()->getContents())->token;
    }


    public function testGetTimezonesUnfilteredSuccess(): void
    {
        $response = self::$client->get('/api/v1/proxmox/timezones');
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public static function testGetTimezonesWithFilterSuccess(): void
    {
        $response = self::$client->get('/api/v1/proxmox/timezones');
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());


    }
/*
php bin/phpunit tests/Proxmox/Common/Timezones/Presentation/Rest/V1/E2EGetTimezonesTest.php
    public function testGetTimezones(): void
    {
        $response = self::$client->get('/api/v1/proxmox/ns100x/networks');

        $this->assertEquals(204, $response->getStatusCode());
    }
    */
}