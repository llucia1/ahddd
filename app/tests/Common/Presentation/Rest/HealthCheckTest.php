<?php


namespace Common\Presentation\Rest;


use GuzzleHttp\Client;

class HealthCheckTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @return void
     */
    public function test_Get_HealthCheck():void
    {

        $client = new Client(['base_uri' => 'http://localhost:80']);

        $response = $client->Get('/api/v1/healthcheck');

        $this->assertEquals(200,$response->getStatusCode());
    }
}