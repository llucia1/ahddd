<?php
declare(strict_types=1);

namespace Net\Ip4\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use Symfony\Component\HttpFoundation\Response as ResponseCode;
class E2EGetIp4ByUuidTest extends WebTestCase
{
    protected static Client $client;
    protected static $token;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK),
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
    }

    public function testGetIp4ByUUIDOK(): void
    {
        $response = self::$client->get('/api/v1/ip4', [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ]]);
        $uuids = json_decode($response->getBody()->getContents(), true);

        if (!empty($uuids)) {
            $uuid = $uuids[0]['uuid'];
            $this->assertIp4ByUUID($uuid);
        } else {
            self::markTestSkipped('No IP4 in Database');
        }
    }

    public function testGetIp4ByUUIDNotExist(): void
    {
        $faker = FakerFactory::create();
        $response = self::$client->request('GET', '/api/v1/ip4/' . $faker->uuid(), [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ],
            'http_errors' => false
        ]);
    
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    
        $body = json_decode($response->getBody()->getContents(), true);
        self::assertEquals('Not Found Ip4s', $body['error']);

    }

    private function assertIp4ByUUID(string $uuid): void
    {
        $response = self::$client->get('/api/v1/ip4/' . $uuid, [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ]]);
        self::assertEquals(ResponseCode::HTTP_OK, $response->getStatusCode());
    }
}// php bin/phpunit tests/Net/Ip4/Presentation/Rest/V1/E2EGetIp4ByUuidTest.php