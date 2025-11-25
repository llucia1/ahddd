<?php
declare(strict_types=1);

namespace Net\Ip4FloatGroup\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2ECreateIp4FloatGroupTest extends WebTestCase
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
            "email" => "xavi@xavi.com",
            "password" => "password",
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


    public function testCreateIpFloatGroupSuccess(): void
    {
        $response = self::$client->post('/api/v1/float_group', ['json' => self::$CORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public static function testCreateIpFloatGroupConflict(): void
    {
        $response = self::$client->request('POST', '/api/v1/float_group', ['http_errors' => false, 'json' => self::$CORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public static function testCreateIpFloatGroupBadRequest(): void
    {

        $response = self::$client->request('POST', '/api/v1/float_group', ['http_errors' => false, 'json' => self::$INCORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateIpFloatGroupNotBodySend(): void
    {

        $response = self::$client->request('POST', '/api/v1/float_group', ['http_errors' => false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateIpFloatGroupDataIncorrect(): void
    {

        $response = self::$client->request('POST', '/api/v1/float_group', ['http_errors' => false, ['body' => self::$PARAMETER_INCORRECT_REQUEST_DATA]]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}