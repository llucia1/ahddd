<?php
declare(strict_types=1);
namespace Tests\User\Presentation\Rest\V1;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

class E2EGetAllUsersTest extends WebTestCase
{

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
                'email' => 'admin@admin.com',
                'password' => 'password'
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];
    }

    public function testGetUserSuccess(): void
    {
        $response = self::$client->get('/api/v1/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ]
        ]);

        $this->assertEquals(ResponseCode::HTTP_OK, $response->getStatusCode());
    }
}