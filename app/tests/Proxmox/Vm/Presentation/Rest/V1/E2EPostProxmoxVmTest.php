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


class E2EPostProxmoxVmTest extends WebTestCase
{
    const ERROR = 'Error';
    const BEARER = 'Bearer';
    protected static Client $client;
    protected static string $token;
    protected static array $data;
    protected static string $url;

    protected static string $uri = '/v1/pve/vm';

    public static function setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();

        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_CREATED, [], json_encode(['uuid' => $faker->uuid()]))
        ]);

        self::setUpClient($mock);

        $response = self::$client->post('/api/v1/auth/login', [
            'json' => [
                'email' => 'admin@admin.com',
                'password' => 'password'
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];

        self::$data = [
            "gcp_node" => "ns1047",
            "vm_name" => $faker->word(),
            "vm_net_ip" => $faker->ipv4(),
            "disk_size" => 20,
            "storage" => "local",
            "net_bridge" => "vmbr0",
            "cores" => 2,
            "memory" => 4096,
            "traffic_limit" => 100,
            "vm_username" => "admin",
            "vm_password" => "password",
            "vm_os" => "ubuntu"
        ];

        self::$url = self::$uri;
    }

    private static function setUpMockHandler(array $responses): void
    {
        $mock = new MockHandler($responses);
        self::setUpClient($mock);
    }

    private static function setUpClient(MockHandler $mock): void
    {
        $handler = HandlerStack::create($mock);
        self::$client = new Client(['handler' => $handler, 'base_uri' => '']);
    }

    public static function testCreateVmSuccess(): void
    {
        self::setUpMockHandler([
            new Response(ResponseCode::HTTP_CREATED, [], json_encode(["uuid" => "ae901ebb-656f-44ff-b7d4-80bae65629c2"]))
        ]);

        $response = self::$client->post(self::$url, [
            'http_errors' => false,
            'headers' => [
                'Authorization' => self::BEARER . ' ' . self::$token
            ],
            'json' => self::$data
        ]);

        self::assertEquals(ResponseCode::HTTP_CREATED, $response->getStatusCode());
    }

    public static function testCreateVmStorageNotExist(): void
    {
        self::setUpMockHandler([
            new Response(ResponseCode::HTTP_BAD_REQUEST, [], json_encode(["error" => "Storage does not exist"]))
        ]);

        $response = self::$client->post(self::$url, [
            'http_errors' => false,
            'headers' => [
                'Authorization' => self::BEARER . ' ' . self::$token
            ],
            'json' => self::$data
        ]);

        self::assertEquals(ResponseCode::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateVmSoNotFound(): void
    {
        self::setUpMockHandler([
            new Response(ResponseCode::HTTP_NOT_FOUND, [], json_encode(["error" => "Operating system not found"]))
        ]);

        $response = self::$client->post(self::$url, [
            'http_errors' => false,
            'headers' => [
                'Authorization' => self::BEARER . ' ' . self::$token
            ],
            'json' => self::$data
        ]);

        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public static function testCreateVmNodeNotExist(): void
    {
        self::setUpMockHandler([
            new Response(ResponseCode::HTTP_BAD_REQUEST, [], json_encode(["error" => "Node does not exist"]))
        ]);

        $response = self::$client->post(self::$url, [
            'http_errors' => false,
            'headers' => [
                'Authorization' => self::BEARER . ' ' . self::$token
            ],
            'json' => self::$data
        ]);

        self::assertEquals(ResponseCode::HTTP_BAD_REQUEST, $response->getStatusCode());
    }


    // php bin/phpunit tests/Proxmox/Vm/Presentation/Rest/V1/E2EPostProxmoxVmTest.php


}