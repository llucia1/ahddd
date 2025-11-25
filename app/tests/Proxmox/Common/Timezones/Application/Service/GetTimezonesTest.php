<?php
declare(strict_types=1);

namespace Proxmox\Common\Timezones\Application\Service;



use GridCP\Proxmox\Common\Application\Timezones\Service\GetTimezonesService;
use GridCP\Proxmox\Common\Infrastructure\Timezones\TimezonesRepository;
use PHPUnit\Framework\TestCase;

class GetTimezonesTest extends TestCase
{
    protected TimezonesRepository $timeZonesRepository;
    protected array $allTimeZones;
    protected string $filter;
    protected GetTimezonesService $getTimeZones;
    public function setUp(): void
    {
        $this->timeZonesRepository = new TimezonesRepository();
        $this->getTimeZones = new GetTimezonesService($this->timeZonesRepository);

        $this->filter = 'Europe/Bel';
    }
    
    public function testGetAllOK():void
    {
        
        $result = $this->getTimeZones->__invoke();
        $this->assertIsArray($result);
        $this->assertCount(594, $result);
        /*
        $timezones = timezone_identifiers_list();// obtenemos todos los Timeszones
        foreach ($timezones as $timezone) {
            print_r($timezone);
            $this->assertContains($timezone, $result, "The Timezone '$timezone' is not expected in the result.");
        }
        */
    }
    
    public function testGetAllWithFilter():void
    {
        $expectedResult = ["Europe/Belfast", "Europe/Belgrade"];
        $result = $this->getTimeZones->__invoke($this->filter);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertCount(count($expectedResult), $result);
        
        foreach ($result as $timezone) {
            $this->assertContains($timezone, $expectedResult, "The Timezone '$timezone' is not expected in the result.");
        }
    } 
    /* 
    php bin/phpunit tests/Proxmox/Common/Timezones/Application/Service/GetTimezonesTest.php
    */
}