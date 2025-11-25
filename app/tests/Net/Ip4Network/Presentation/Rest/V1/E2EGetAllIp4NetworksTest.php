<?php
declare(strict_types=1);

namespace Net\Ip4Network\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EGetAllIp4NetworksTest extends WebTestCase
{
    protected static Client $client;
    protected static string $JWT;
    protected static array $USER_LOGIN;


    public static function setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "xavi@xavi.com",
            "password" => "password",
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
    public static function testGetIp4NetworkOK(): void
    {
        $response = self::$client->get('/api/v1/ip4_network');
        $uuids = json_decode($response->getBody()->getContents(), true);

        if (!empty($uuids)) {
            self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        } else {
            self::markTestSkipped('No Ip4 Networks in Database');
        }
    }

}