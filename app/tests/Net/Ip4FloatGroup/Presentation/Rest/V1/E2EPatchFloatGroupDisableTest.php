<?php
declare(strict_types=1);

namespace Net\Ip4FloatGroup\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use Symfony\Component\HttpFoundation\Response as ResponseCode;
class E2EPatchFloatGroupDisableTest extends WebTestCase
{
    protected static Client $client;
    protected static string $JWT;
    protected static array $USER_LOGIN;
    
    protected static string $uuid = 'fa0eef2f-b41d-4050-9c48-caba322c3d3a';
    protected static string $uuidNotAssociateNetwork = '92011bcd-ab2b-4100-9fe3-7c54e7f39781';
    protected static string $uuidAssociateNetwork = 'fa0eef2f-b41d-4050-9c48-caba322c3d3a';

    public static function setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "user2@email.es",
            "password" => "user2",
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

    public function testFloatGroupDisableOK(): void
    {
        $response = self::$client->patch('/api/v1/float_group/' . self::$uuidNotAssociateNetwork . '/disable');
        self::assertEquals(ResponseCode::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testFloatGroupNotExistsDisableError(): void
    {
        $faker = FakerFactory::create();
    
        try {
            $response = self::$client->patch('/api/v1/float_group/' . $faker->uuid() . '/disable');
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }
    
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testFloatGroupWithAssociateNetwoksDisableError(): void
    {
        $faker = FakerFactory::create();
    
        try {
            $response = self::$client->patch('/api/v1/float_group/' . self::$uuidAssociateNetwork . '/disable');
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }
    
        self::assertEquals(ResponseCode::HTTP_CONFLICT, $response->getStatusCode());
    }
    
}