<?php
declare(strict_types=1);

namespace Security\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2ECreateAuthUserTest extends WebTestCase
{
   protected static array $data;
   protected static $client;
    public static function  setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();
        self::$data = [
            "email" => $faker->email(),
            "password" => $faker->password(),
            "userName" => $faker->userName(),
            "firstName" => $faker->firstNameMale(),
            "lastName" => $faker->lastName()
        ];
        self::$client = new Client(['base_uri' => 'http://localhost:80']);
    }

    public  static function testCreateAuthUserWhenAllDataIsCorrect():void{
        $response = self::$client->post('/api/v1/auth/register', ['json'=>self::$data]);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public  static function testCreateAuthUserWhenNotBodySend():void{
        $response = self::$client->request('POST', '/api/v1/auth/register',['http_errors'=>false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }


    public static function testCreateAuthUserWhenEmailExist():void{
        $response = self::$client->request('POST', '/api/v1/auth/register',['http_errors'=>false,['body'=>self::$data]]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}