<?php
declare(strict_types=1);

namespace Proxmox\Vm\Presentation\Rest\V1;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EPatchVmTest extends WebTestCase
{
    protected static array $data;
    protected static Client $client;
    protected static array $USER_LOGIN;
    protected static string $JWT;
    protected static string $uuid;

    public static function  setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "admin@admin.com",
            "password" => "password",
        ];


        self::$data = [
            "vmName"=>"VM.nameExample",
            "diskSize" => "20G",
            "cores" => 1,
            "vmOs" => "Debian 12"
        ];
        self::login();
        self::$client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.self::$JWT]]);
        self::getUuid();

    }

    private static function getUuid() {
        $result = self::$client->get('/api/v1/pve/vm');
        $vm = json_decode($result->getBody()->getContents(),true);
        self::$uuid = $vm[0]['uuid'];
    }

    public static function login():void
    {
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>self::$USER_LOGIN]);
        self::$JWT =   json_decode($result->getBody()->getContents())->token;
    }

    public static function testPatchVmSuccess(): void
    {
        $response = self::$client->patch('/api/v1/pve/vm/'.self::$uuid, ['json' => self::$data]);
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public  static function testPatchVmWhenNotBodySend():void{
        $response = self::$client->request('PATCH', '/api/v1/pve/vm/'.self::$uuid,['http_errors'=>false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testPatchVmWhenNotExist():void{
        $response = self::$client->request('PATCH', '/api/v1/pve/vm/'.'uuidNotFound',['http_errors'=>false,['body'=>self::$data]]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
    
// php bin/phpunit tests/Proxmox/Vm/Presentation/Rest/V1/E2EPatchVmTest.php

}