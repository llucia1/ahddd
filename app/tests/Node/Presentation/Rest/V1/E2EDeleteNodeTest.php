<?php
declare(strict_types=1);

namespace Node\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EDeleteNodeTest extends WebTestCase
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
    /**
     * @throws GuzzleException
     */
    public static function testDeleteNodeByUUIDNotExist(): void
    {
        $faker = FakerFactory::create();
        $response = self::$client->request('DELETE','/api/v1/node/'. $faker->uuid(),['http_errors' => false]);
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}