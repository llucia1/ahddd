<?php
declare(strict_types=1);

namespace Tests\Plan\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EGetPlanByUuidTest extends WebTestCase
{
    protected static Client $client;
    protected string $uuid;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client(['base_uri' => 'http://localhost:80']);
    }

    public function testGetPlanByUUIDOK(): void
    {
        
       $response = self::$client->get('/api/v1/plan');
       $uuids = json_decode($response->getBody()->getContents(), true);

        if (!empty($uuids)) {
            $this->uuid = $uuids[0]['uuid'];
            $this->assertPlanByUUID($this->uuid );
        } else {
            self::markTestSkipped('No Plans in Database');
        }
    }

    public function testGetPlanByUUIDNotExist(): void
    {
        $faker = FakerFactory::create();
        $response = self::$client->request('GET', '/api/v1/plan/' . $faker->uuid() , ['http_errors' => false]);
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
    public function testNotValidUUID(): void
    {
        $uuid = '05f7d463-xxxx-4b5b-8616-8d29711f951x';
        $response = self::$client->request('GET', '/api/v1/plan/' . $uuid, ['http_errors' => false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    private function assertPlanByUUID(string $uuid): void
    {
        $response = self::$client->get('/api/v1/plan/' . $uuid);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}