<?php
declare(strict_types=1);

namespace Proxmox\Version\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response as ResponseCode;


class E2EGetUserTest extends WebTestCase
{
    protected static $client;
    protected static $token;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK),
            new Response(ResponseCode::HTTP_NO_CONTENT)
        ]);

        $handlerStack = HandlerStack::create($mock);
        self::$client = new Client(['handler' => $handlerStack]);

        $response = self::$client->post('/api/v1/auth/login', [
            'json' => [
                'email' => 'user2@email.es',
                'password' => 'user2'
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];
    }

    public function testGetUserSuccess(): void
    {
        $response = self::$client->get('/api/v1/user/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ]
        ]);

        $this->assertEquals(ResponseCode::HTTP_OK, $response->getStatusCode());
    }
    
    public function testGetUserNotFound(): void
    {
        $response = self::$client->get('/api/v1/user/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ]
        ]);

        $this->assertEquals(ResponseCode::HTTP_NO_CONTENT, $response->getStatusCode());
    }
     
}