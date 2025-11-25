<?php
declare(strict_types=1);

namespace Proxmox\Storage\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;




class E2EGetProxmoxStorageTest extends WebTestCase
{
    const ERROR = 'Error';
    const BEARER = 'Bearer';
    protected static Client $client;
    protected static array $USER_LOGIN;
    protected static string $JWT;
    protected static string $node_name='ns1048';
    protected static $token;
    protected static array $data;
    protected static string $url;
    protected static array $dataNotFound;
    protected static $uuid = 'ae901ebb-656f-44ff-b7d4-80bae65629c2';

    protected static $uri = '/api/v1/pve/';
    protected static $resource = '/storage';

    public static function setUpBeforeClass(): void
    {
        
        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_NOT_FOUND),
            new Response(ResponseCode::HTTP_SERVICE_UNAVAILABLE),
            new Response(ResponseCode::HTTP_UNAUTHORIZED),
            new Response(ResponseCode::HTTP_BAD_REQUEST),
            new Response(ResponseCode::HTTP_NO_CONTENT)
        ]);

        HandlerStack::create($mock);
        self::$client = new Client(['base_uri' => 'http://localhost:80']);


        $response = self::$client->post('/api/v1/auth/login', [
            'json' => [
                'email' => 'xavi@xavi.com',
                'password' => 'password'
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];

        self::$url = self::$uri . self::$uuid . self::$resource;
    }

    public static function getProxmoxStorage(string $uri): ?ResponseInterface {
        try {
            return self::$client->get($uri, [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => self::BEARER . ' ' . self::$token
                ]
            ]);
        } catch (\Exception $e) {
            echo self::ERROR . ': ' . $e->getMessage();
            return null;
        }
    }
    

    public static function testGetProxmoxStorageOK(): void
    {
        $response = self::getProxmoxStorage(self::$uri. self::$node_name .self::$resource);
        self::assertEquals(ResponseCode::HTTP_OK, $response->getStatusCode());
    }
    // php bin/phpunit tests/Proxmox/Storage/Presentation/Rest/V1/E2EGetProxmoxStorageTest.php

}