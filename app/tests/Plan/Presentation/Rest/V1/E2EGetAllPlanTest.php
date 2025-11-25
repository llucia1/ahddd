<?php
declare(strict_types=1);

namespace Tests\Plan\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EGetAllPlanTest extends WebTestCase
{
    protected static Client $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client(['base_uri' => 'http://localhost:80']);
    }

    public function testGetAllPlanOK(): void
    {
        
        $response = self::$client->get('/api/v1/plan');
        $uuids = json_decode($response->getBody()->getContents(), true);

        if (!empty($uuids)) {
            self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        } else {
            self::markTestSkipped('Not Found Plan');
        }
    }
    // php bin/phpunit tests/Plan/Presentation/Rest/V1/E2EGetAllPlanTest.php
}