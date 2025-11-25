<?php
declare(strict_types=1);

namespace Proxmox\Vm\Presentation\Rest\V1;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Faker\Factory as FakerFactory;

class E2EPatchPlanTest extends WebTestCase
{
    protected static array $data;
    protected static Client $client;
    protected static array $USER_LOGIN;
    protected static string $JWT;
    protected static mixed $plan;
    protected static mixed $planDuplicate;
    public static function  setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "xavi@xavi.com",
            "password" => "password",
        ];
        
        self::login();
        self::$client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.self::$JWT]]);
        self::getPlan();

    }

    private static function getPlan() {
        $result = self::$client->get('/api/v1/plan');
        $plan = json_decode($result->getBody()->getContents(),true);
        self::$plan = $plan[0];
        self::$planDuplicate = $plan[1];
    }

    public static function login():void
    {
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>self::$USER_LOGIN]);
        self::$JWT =   json_decode($result->getBody()->getContents())->token;
    }

    public static function testPatchPlanSuccess(): void
    {
        $response = self::$client->patch('/api/v1/plan/'.self::$plan['uuid'], ['json' => self::$plan]);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public  static function testPatchPlanWhenNotBodySend():void{
        $response = self::$client->request('PATCH', '/api/v1/plan/'.self::$plan['uuid'],['http_errors'=>false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testPatchPlanWhenNotExist():void{
        $data = self::$planDuplicate;
        $faker = FakerFactory::create();
        try {
            $response = self::$client->request('PATCH', '/api/v1/plan/' . $faker->uuid() , ['json' => $data]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
         }
    }
    
// php bin/phpunit tests/Plan/Presentation/Rest/V1/E2EPatchPlanTest.php

}