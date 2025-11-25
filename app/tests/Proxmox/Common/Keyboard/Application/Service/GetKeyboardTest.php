<?php
declare(strict_types=1);

namespace Proxmox\Common\Keyboard\Application\Service;

use GridCP\Proxmox\Common\Application\Keyboard\Service\GetKeyboardService;
use GridCP\Proxmox\Common\Domain\Keyboard\Repository\IKeyboardService;
use PHPUnit\Framework\TestCase;

class GetKeyboardTest extends TestCase
{
    protected IKeyboardService $keyboardRepository;
    protected array $allkeyboard;
    protected string $filter;
    protected GetKeyboardService $getKeyboard;
    public function setUp(): void
    {
        $this->keyboardRepository = $this->getMockBuilder(IKeyboardService::class)
                                         ->getMock();
        $this->getKeyboard = new GetKeyboardService($this->keyboardRepository);

        $this->filter = 'es';
    }
    
    public function testGetAllOK():void
    {
        $results = [
            "de",
            "de-ch",
            "da",
            "en-gb",
            "en-us",
            "es",
            "fi",
            "fr",
            "fr-be",
            "fr-ca",
            "fr-ch",
            "hu",
            "is",
            "it",
            "ja",
            "lt",
            "mk",
            "nl",
            "no",
            "pl",
            "pt",
            "pt-br",
            "sv",
            "sl",
            "tr"
          ];
          $this->keyboardRepository->expects($this->once())
                                   ->method('gets')
                                   ->willReturn($results);
        $resultService = $this->getKeyboard->__invoke();
        $this->assertIsArray($resultService);
        $this->assertCount(25, $resultService);
    }
    
    public function testGetAllWithFilter():void
    {
        $results = ["es"];
                        
        $this->keyboardRepository ->expects($this->once())
                        ->method('filter')
                        ->with($this->filter)
                        ->willReturn($results);
        $resultsService = $this->getKeyboard->__invoke($this->filter);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertCount(count($resultsService), $results);
        
        foreach ($resultsService as $kb) {
            $this->assertContains($kb, $results, "The Keyboard '$kb' is not expected in the result.");
        }
    } 
    /* 
    php bin/phpunit tests/Proxmox/Common/Keyboard/Application/Service/GetKeyboardTest.php
    */
}