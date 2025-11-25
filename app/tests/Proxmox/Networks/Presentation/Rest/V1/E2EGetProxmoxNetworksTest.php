<?php
declare(strict_types=1);

namespace Proxmox\Networks\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EGetProxmoxNetworksTest extends WebTestCase
{
    protected static array $data;
    protected static Client $client;
    protected static array $USER_LOGIN;
    protected static string $JWT;

    public static function  setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "xavi@xavi.com",
            "password" => "password",
        ];
        self::$data = [
            "proxmox_node_name" => 'ns1000',
        ];
        self::login();
        self::$client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.self::$JWT]]);
    }
    public static function login():void
    {
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>self::$USER_LOGIN]);
        self::$JWT =   json_decode($result->getBody()->getContents())->token;
    }

    public static function testGetNetworksSuccess(): void
    {
        $response = self::$client->get('/api/v1/proxmox/' . self::$data["proxmox_node_name"] . '/networks');
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    } 
    public function testGetProxmoxNetworksNotFound(): void
    {
        $response = self::$client->get('/api/v1/proxmox/ns100x/networks');

        $this->assertEquals(204, $response->getStatusCode());
    }

}