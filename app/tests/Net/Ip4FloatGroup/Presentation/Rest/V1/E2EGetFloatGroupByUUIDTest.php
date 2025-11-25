<?php
declare(strict_types=1);

namespace Net\Ip4FloatGroup\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use Symfony\Component\HttpFoundation\Response as ResponseCode;
class E2EGetFloatGroupByUUIDTest extends WebTestCase
{
    protected static Client $client;
    protected static string $JWT;
    protected static array $USER_LOGIN;

    protected static string $uuid = '63caef69-e554-4dfe-8878-3ec605aaf76b';

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

    public function testGetFloatGroupByUUIDOK(): void
    {
        $response = self::$client->get('/api/v1/float_group/' . self::$uuid);
        self::assertEquals(ResponseCode::HTTP_OK, $response->getStatusCode());
    }

    public function testGetFloatGroupByUUIDNotExist(): void
    {
        try {
            $faker = FakerFactory::create();
            self::$client->get('/api/v1/float_group/' . $faker->uuid());
        }catch (\Exception $ex) {
            self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $ex->getCode());
        }
    }
}