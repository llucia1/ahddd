<?php
declare(strict_types=1);

namespace Proxmox\Vm\Presentation\Rest\V1;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Exception;
use Faker\Factory as FakerFactory;

use GuzzleHttp\Exception\ClientException;


use Symfony\Component\HttpFoundation\Response as ResponseCode;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;


class E2EAddIp4ToAVmTest extends WebTestCase
{
    const ERROR = 'Error';
    const BEARER = 'Bearer';
    protected static Client $client;
    protected static $token;
    protected static array $data;
    protected static string $url;
    protected static string $vmUuid;
    protected static array $dataNotFound;
    protected static array $badData =  [
        "ip" => 'invalid-ip-address'
    ];
    protected static $uuid = 'ae901ebb-656f-44ff-b7d4-80bae65629c2';

    protected static $uri = '/v1/pve/vm/';
    protected static $resource = '/ip4';

    public static function setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();

        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK, [], json_encode([['uuid' => $faker->uuid()]])),
            new Response(ResponseCode::HTTP_NO_CONTENT),
            new Response(ResponseCode::HTTP_CONFLICT),
            new Response(ResponseCode::HTTP_NOT_FOUND),
            new Response(ResponseCode::HTTP_BAD_REQUEST)
        ]);

        self::setUpClient($mock);

        $response = self::$client->post('/api/v1/auth/login', [
            'json' => [
                'email' => 'xavi@xavi.com',
                'password' => 'password'
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];

        self::$vmUuid = $faker->uuid();
        self::$data = [
            "ip" => $faker->ipv4()
        ];

        self::$dataNotFound = [
            "ip" => $faker->ipv4()
        ];

        self::$url = self::$uri . self::$vmUuid . self::$resource;
    }

    public static function addVmToIp4(string $uri, array $data): ?ResponseInterface
    {
        try {
            return self::$client->post($uri, [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => self::BEARER . ' ' . self::$token
                ],
                'json' => $data
            ]);
        } catch (Exception $e) {
            echo self::ERROR . ': ' . $e->getMessage();
            return null;
        }
    }

    private static function setUpMockHandler(array $responses): void
    {
        $mock = new MockHandler($responses);
        self::setUpClient($mock);
    }

    private static function setUpClient(MockHandler $mock): void
    {
        $handler = HandlerStack::create($mock);
        self::$client = new Client(['handler' => $handler, 'base_uri' => 'http://localhost:80']);
    }

    public static function testAddVmToIp4IsCorrect(): void
    {
        self::setUpMockHandler([
            new Response(ResponseCode::HTTP_NO_CONTENT)
        ]);

        $response = self::addVmToIp4(self::$url, self::$data);
        self::assertEquals(ResponseCode::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public static function testAddVmToIp4Duplicated(): void
    {
        self::setUpMockHandler([
            new Response(ResponseCode::HTTP_CONFLICT)
        ]);

        $response = self::addVmToIp4(self::$url, self::$data);
        self::assertEquals(ResponseCode::HTTP_CONFLICT, $response->getStatusCode());
    }

    public static function testWhenVmNotFound(): void
    {
        self::setUpMockHandler([
            new Response(ResponseCode::HTTP_NOT_FOUND)
        ]);

        $faker = FakerFactory::create();
        $uri = self::$uri . $faker->uuid() . self::$resource;

        $response = self::addVmToIp4($uri, self::$data);
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public static function testBadData(): void
    {
        self::setUpMockHandler([
            new Response(ResponseCode::HTTP_BAD_REQUEST)
        ]);

        $response = self::addVmToIp4(self::$url, self::$badData);
        self::assertEquals(ResponseCode::HTTP_BAD_REQUEST, $response->getStatusCode());
    }


    // php bin/phpunit tests/Proxmox/Vm/Presentation/Rest/V1/E2EAddIp4ToAVmTest.php


}