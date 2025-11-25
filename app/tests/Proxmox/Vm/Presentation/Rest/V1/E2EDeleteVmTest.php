<?php
declare(strict_types=1);

namespace Proxmox\Vm\Presentation\Rest\V1;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory as FakerFactory;
class E2EDeleteVmTest extends WebTestCase
{
    protected static array $data;
    protected static Client $client;
    protected static array $USER_LOGIN;
    protected static string $JWT;
    protected static string $uuid;

    public static function  setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "xavi@xavi.com",
            "password" => "password",
        ];

        self::login();
        self::$client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.self::$JWT]]);
        self::getUuid();

    }

    private static function getUuid() {
        $result = self::$client->get('/api/v1/pve/vm');
        self::$uuid = json_decode($result->getBody()->getContents(),true)[0]['uuid'];
    }

    public static function login():void
    {
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>self::$USER_LOGIN]);
        self::$JWT =   json_decode($result->getBody()->getContents())->token;
    }

    public static function testDeleteVmSuccess(): void
    {
        $response = self::$client->delete('/api/v1/pve/vm/'.self::$uuid);
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
    
    public static function testDeleteVmWhenNotExist():void{
        $faker = FakerFactory::create();
        $response = self::$client->request('DELETE', '/api/v1/pve/vm/'. $faker->uuid(),['http_errors' => false]);
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
    
// php bin/phpunit tests/Proxmox/Vm/Presentation/Rest/V1/E2EDeleteVmTest.php

}