<?php
declare(strict_types=1);

namespace Net\Ip4Network\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EPatchIP4NetworkTest extends WebTestCase
{
    protected static array $data;
    protected static Client $client;
    protected static array $USER_LOGIN;
    protected static string $JWT;
    protected static string $uuid;
    protected static string $route = '/api/v1/ip4_network';

    public static function  setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "xavi@xavi.com",
            "password" => "password",
        ];

        $faker = FakerFactory::create();
        
        self::$data = [
            "name" => $faker->name(),
            "name_server1" => $faker->ipv4(),
            "name_server2" => $faker->ipv4(),
            "name_server3" => $faker->ipv4(),
            "name_server4" => $faker->ipv4(),
            "priority" => $faker->randomNumber(2),
            "free" => $faker->randomNumber(2),
            "netmask" => "255.255.255.0",
            "gateway" => "192.168.1.1",
            "broadcast" => $faker->ipv4(),
            "selectable_by_client" => true,
            "no_arp" => true,
            "rir" => true,
            "active" => true
        ];

        self::login();
        self::$client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.self::$JWT]]);
        self::getUuid();
    }
    
    private static function getUuid() {
        $result = self::$client->get( self::$route );
        self::$uuid = json_decode($result->getBody()->getContents(),true)[0]['uuid'];
    }

    public static function login():void
    {
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>self::$USER_LOGIN]);
        self::$JWT =   json_decode($result->getBody()->getContents())->token;
    }

    public static function testPatchSuccess(): void
    {
        $response = self::$client->patch( self::$route . '/'.self::$uuid, ['json' => self::$data]);
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public  static function testPatchWhenNotBodySend():void{
        $response = self::$client->request('PATCH', self::$route . '/'.self::$uuid,['http_errors'=>false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testPatchWhenNotExist():void{
        $response = self::$client->request('PATCH', self::$route . '/'.'uuidNotFound',['http_errors'=>false,['body'=>self::$data]]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
    
// php bin/phpunit tests/Net/Ip4Network/Presentation/Rest/V1/E2EPatchIP4NetworkTest.php    
    
    
}