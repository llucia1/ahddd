<?php
declare(strict_types=1);

namespace Proxmox\Common\Keyboard\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EGetKeyboardTest extends WebTestCase
{
    protected static Client $client;
    protected static array $USER_LOGIN;

    protected static string $JWT;

    public static function setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();

        self::$USER_LOGIN = [
            "email" => "user2@email.es",
            "password" => "user2"
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
        $response = self::$client->get('/api/v1/proxmox/Keyboard');
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public static function testGetTimezonesWithFilterSuccess(): void
    {
        $filter = 'de';
        $response = self::$client->get('/api/v1/proxmox/Keyboard?filter='.$filter);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());


    }
/*
php bin/phpunit tests/Proxmox/Common/Keyboard/Presentation/Rest/V1/E2EGetKeyboardTest.php
    */
}