<?php
declare(strict_types=1);

namespace Net\Ip4Tag\Presentation;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2ECreateIp4TagTest extends WebTestCase
{
    protected static Client $client;
    protected static array $CORRECT_REQUEST_DATA;
    protected static array $INCORRECT_REQUEST_DATA;
    protected static array $PARAMETER_INCORRECT_REQUEST_DATA;
    public static function setUpBeforeClass(): void
    {

        $faker = FakerFactory::create();
        self::$CORRECT_REQUEST_DATA = [
            "id_ip" => $faker->randomNumber(1),
            "tag" => $faker->name(),
        ];

        self::$INCORRECT_REQUEST_DATA = [
            "tag" => $faker->name()
        ];
        self::$PARAMETER_INCORRECT_REQUEST_DATA = [
            "id_ip" => $faker->randomNumber(1),
            "tag" => 1,
        ];

        self::$client = new Client(['base_uri' => 'http://localhost:80']);
    }

    public static function testCreateIp4TagSuccess(): void
    {
        $response = self::$client->post('/api/v1/ip4_tag', ['json' => self::$CORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public static function testCreateIp4TagNameConflict(): void
    {
        $response = self::$client->request('POST', '/api/v1/ip4_tag', ['http_errors' => false, 'json' => self::$CORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public static function testCreateIp4TagBadRequest(): void
    {
        $response = self::$client->request('POST', '/api/v1/ip4_tag', ['http_errors' => false, 'json' => self::$INCORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateIp4TagNotBodySent(): void
    {
        $response = self::$client->request('POST', '/api/v1/ip4_tag', ['http_errors' => false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateIp4TagIncorrectData(): void
    {
        $response = self::$client->request('POST', '/api/v1/ip4_tag', ['http_errors' => false, 'json' => self::$PARAMETER_INCORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}