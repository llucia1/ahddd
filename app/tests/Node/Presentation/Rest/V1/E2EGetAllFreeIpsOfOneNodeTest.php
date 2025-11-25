<?php
declare(strict_types=1);

namespace Node\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception\ClientException;

class E2EGetAllFreeIpsOfOneNodeTest extends WebTestCase
{
    protected static Client $client;
    protected static string $JWT;
    protected static array $USER_LOGIN;

    public static function setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "admin@admin.com",
            "password" => "password",
        ];
        self::login();
        self::$client = new Client([
            'base_uri' => 'http://localhost',
            'headers' => ['Authorization' => 'Bearer ' . self::$JWT]
        ]);
    }

    public static function login(): void
    {
        $client = new Client(['base_uri' => 'http://localhost']);
        $result = $client->post('/api/v1/auth/login', ['json' => self::$USER_LOGIN]);
        self::$JWT = json_decode($result->getBody()->getContents())->token;
    }

    public function testGetFreeIpsSuccess(): void
    {
        $response = self::$client->get('/api/v1/node');
        $nodes = json_decode($response->getBody()->getContents(), true);
    
        if (!empty($nodes)) {
            $uuid = $nodes[0]['uuid'];
            $response = self::$client->get("/api/v1/node/{$uuid}/ips/free", [
                'http_errors' => false
            ]);
            self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
            $ips = json_decode($response->getBody()->getContents(), true);
            self::assertIsArray($ips);
        
            foreach ($ips as $ip) {
                self::assertArrayHasKey('uuid', $ip);
                self::assertArrayHasKey('ip', $ip);
                self::assertArrayHasKey('network', $ip);
                self::assertArrayHasKey('priority', $ip);
            }
        } else {
            self::markTestSkipped('No nodes in DB');
        }
    }

    public function testNodeNotExist404(): void
    {
        $faker = FakerFactory::create();
        $fakeUuid = $faker->uuid;

        $response = self::$client->get("/api/v1/node/{$fakeUuid}/ips/free", [
            'http_errors' => false
        ]);

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testNodeWithoutFreeIps409(): void
    {
        $response = self::$client->get('/api/v1/node', [
            'query' => [
                'clientUuid' => null
            ],
            'http_errors' => false
        ]);
        $nodes = json_decode($response->getBody()->getContents(), true);

        if (!empty($nodes)) {
            $uuid = $nodes[0]['uuid'];

            $response = self::$client->get("/api/v1/node/{$uuid}/ips/free", [
                            
                            'http_errors' => false
                        ]);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    $ips = json_decode($response->getBody()->getContents(), true);
                    self::assertNotEmpty($ips, "Not Find Free Ips.");
                } else {
                    self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
                }
        } else {
            self::markTestSkipped('Not Found Nodes');
        }
    }
    

}
// php bin/phpunit tests/Node/Presentation/Rest/V1/E2EGetAllFreeIpsOfOneNodeTest.php