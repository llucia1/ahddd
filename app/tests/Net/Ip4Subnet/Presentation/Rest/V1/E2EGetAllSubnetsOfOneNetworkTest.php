<?php
declare(strict_types=1);

namespace Net\Ip4Subnet\Presentation\Rest\V1;



use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;


use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use PHPUnit\Framework\TestCase;
class E2EGetAllSubnetsOfOneNetworkTest extends TestCase
{
    const ERROR = 'Error';
    const BEARER = 'Bearer';
    protected static Client $client;
    protected static string $token;
    protected static string $url;
    protected static string $uuid;

    protected static string $uri = '/v1/ip4/subnet/network';

    public static function setUpBeforeClass(): void
    {
        $faker = \Faker\Factory::create();

        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK, [], json_encode([['uuid' => $faker->uuid()]])),
            new Response(ResponseCode::HTTP_OK, [], json_encode([
                [
                    'uuid' => $faker->uuid(),
                    'ip' => $faker->ipv4(),
                    'mask' => 24,
                    'floatgroupUuid' => $faker->uuid(),
                ],
                [
                    'uuid' => $faker->uuid(),
                    'ip' => $faker->ipv4(),
                    'mask' => 24,
                    'floatgroupUuid' => $faker->uuid(),
                ],
            ])),
            new Response(ResponseCode::HTTP_NOT_FOUND),
            new Response(ResponseCode::HTTP_BAD_REQUEST),
        ]);

        self::setUpNetwork($mock);

        $response = self::$client->post('/api/v1/auth/login', [
            'json' => [
                'email' => 'xavi@xavi.com',
                'password' => 'password',
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];

        self::$uuid = $faker->uuid();
        self::$url = self::$uri;
    }

    public static function getAllSubnetsForNetwork(string $uri, string $networkUuid): ?\Psr\Http\Message\ResponseInterface
    {
        try {
            return self::$client->get($uri . '/' . $networkUuid, [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => self::BEARER . ' ' . self::$token
                ],
            ]);
        } catch (\Exception $e) {
            echo self::ERROR . ': ' . $e->getMessage();
            return null;
        }
    }

    private static function setUpNetwork(MockHandler $mock): void
    {
        $handler = HandlerStack::create($mock);
        self::$client = new Client(['handler' => $handler, 'base_uri' => 'http://localhost:80']);
    }

    public static function testGetAllSubnetsForNetworkIsCorrect(): void
    {
        $response = self::getAllSubnetsForNetwork(self::$url, self::$uuid);
        self::assertEquals(ResponseCode::HTTP_OK, $response->getStatusCode());
    }

    public static function testGetAllSubnetsForNetworkNotFound(): void
    {
        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_NOT_FOUND),
        ]);
        self::setUpNetwork($mock);

        $response = self::getAllSubnetsForNetwork(self::$url, self::$uuid);
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public static function testGetAllSubnetsForNetworkInvalidUuid(): void
    {
        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_BAD_REQUEST),
        ]);
        self::setUpNetwork($mock);

        $response = self::getAllSubnetsForNetwork(self::$url, 'invalid-uuid');
        self::assertEquals(ResponseCode::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
    // php bin/phpunit tests/Net/Ip4Subnet/Presentation/Rest/V1/E2EGetAllSubnetsOfOneNetworkTest.php
}





