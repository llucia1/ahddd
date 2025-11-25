<?php
declare(strict_types=1);

namespace Node\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class E2EPacthNodeTest extends WebTestCase
{
    protected static array $data;
    protected static $client;
    protected static $uuid = '1b52504c-78a3-4743-8e7e-ccdc0181f03f';
    protected static $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MTIxNDU0NjUsImV4cCI6MTcxMjE0OTA2NSwiZW1haWwiOiJ1c2VyMkBlbWFpbC5lcyIsInV1aWQiOiI0MmE2NzFkMS0zNGEyLTQ2ZjYtYjFhZC01M2E0Y2I5NGIzYTgiLCJ1c2VybmFtZSI6InVzZXIyIn0.iIn29DrSkRfgqqsWYz_HJtd0FzxcO5yLzmuDgjpDzZv3qL2zRHR4LJWvpOR0-3IYuusmcmm-0w2t5XJXVQyWZvNy0bjmBOgU1tSs5KN7lXtBX4WvqVN0dT2rpWzaZLETrMtaAz9apz9jQ492UsoJ6vk2SexJgrVKGK3C3yvBGs2jtY-qnd04-eP8-WIxm3KbI8lZw4iq91NpwCWJSMDROkRoc6-PsbR_kTxxlMsdI4KxTdlXyKf1o451n53Bg1GEtIWWHc-3ktp0bJXJBZZ1FPyxNMm3iFqIZSwKzOJfLpdT-EH2-LrnjYEI2PBBaiL3CRzJGlJ3ztQcZQ_zIIkI5Q';


    public static function  setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();
        
        self::$data = [
            "name" => $faker->name(),
            "proxmox_hostname" => $faker->name(),
            "proxmox_username" => $faker->userName(),
            "proxmox_password" => $faker->password(),
            "proxmox_realm" => "pam",
            "proxmox_port"=> 8006,
            "ip" => $faker->ipv4(),
            "ssh_port" => $faker->randomNumber(4),
            "timezone" => $faker->timezone(),
            "keyboard" => $faker->randomLetter(),
            "display" => $faker->text(20),
            "storage" => $faker->text(20),
            "storage_iso" => $faker->text(20),
            "storage_image" => $faker->text(20),
            "storage_backup" => $faker->text(20),
            "network_interface" => $faker->text(20)
        ];

        self::$client = new Client([
            'base_uri' => 'http://localhost:80',
        ]);
    }

    private static function getHeaders(): array
    {
        return [
            'CONTENT_TYPE' => 'application/json',
            'Authorization' => 'Bearer ' . self::$accessToken
        ];
    }
    


    public static function testPatchNodeWhenAllDataIsCorrect(): void
    {
        $headers = self::getHeaders();

        $response = self::$client->request('PATCH', '/api/v1/node/' . self::$uuid, [
            'headers' => $headers,
            'json' => self::$data,
        ]);

        self::assertEquals(HttpResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }    

    public  static function testPacthNodeWhenNotBodySend():void{

        $headers = self::getHeaders();

        $response = self::$client->request('PATCH', '/api/v1/node/'. self::$uuid,[
                                                                                    'headers' => $headers,
                                                                                    'http_errors'=>false
                                                                                ]);
        self::assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testPacthNodeWhenNameExist():void{
        $headers = self::getHeaders();
        $response = self::$client->request('PATCH', '/api/v1/node/'. self::$uuid,[
                                                                                    'headers' => $headers,
                                                                                    'http_errors'=>false,
                                                                                    ['body'=>self::$data]
                                                                                ]);
        self::assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
    
    
}