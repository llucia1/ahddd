<?php
declare(strict_types=1);

namespace Proxmox\Version\Application\Service;

use GridCP\User\Application\Service\PostUserAddAllowedCountriesService;
use GridCP\User\Domain\Exception\CountryNoFound;
use GridCP\User\Domain\Exception\UserNoFound;
use GridCP\User\Domain\Repository\ICountriesRepository;
use GridCP\User\Domain\Repository\IUserCountriesRepository;
use GridCP\User\Domain\Repository\IUserRepository;
use GridCP\User\Domain\VO\CountriesVo;
use GridCP\User\Domain\VO\UserUuid;
use GridCP\User\Infrastructure\DB\MySQL\Entity\CountryEntity;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserCountriesEntity;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserEntity;
use PHPUnit\Framework\TestCase;



use Psr\Log\LoggerInterface;
use Tests\Plan\Application\Helper\PlanTools;

class PostAddCoutriesAllowToUserTest extends TestCase
{

    use PlanTools;

    protected IUserRepository $userRepository;
    protected IUserCountriesRepository $countryUserRepository;
    protected ICountriesRepository $countryRepository;

    private PostUserAddAllowedCountriesService $userAddAllowedCountriesService;

    private string $userUuid;
    private array $countries;
    private UserEntity $user;
    private UserCountriesEntity $userCountry;
    private CountryEntity $country;
    private CountriesVo $countriesVo;
    private UserUuid $userUuidVo;

    public function setUp(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->userRepository = $this->getMockBuilder( IUserRepository::class )->disableOriginalConstructor()->getMock();
        $this->countryUserRepository = $this->getMockBuilder( IUserCountriesRepository::class )->disableOriginalConstructor()->getMock();
        $this->countryRepository = $this->getMockBuilder( ICountriesRepository::class )->disableOriginalConstructor()->getMock();
        $this->userAddAllowedCountriesService = new PostUserAddAllowedCountriesService($this->userRepository, $this->countryUserRepository,$this->countryRepository,$loggerMock);

        $this->userUuid = 'ae901ebb-656f-44ff-b7d4-80bae65629c2';

        $this->user = new UserEntity();
        $this->user->setId(1);
        $this->user->setUuid($this->userUuid);
        $this->user->setEmail('xavi@xavi.com');
        $this->user->setFirstName('firstName');
        $this->user->setLastName('lastName');
        $this->user->setUuidAuth('dd1f24cb-3923-4117-9a50-1ece75d06000');

        $this->country = new CountryEntity();
        $this->country->setId(64);
        $this->country->setName('Spain');
        $this->country->setCode('ES');
        $this->country->setCode3('ESP');

        $this->userCountry = new UserCountriesEntity();
        $this->userCountry->setId(1);
        $this->userCountry->setUser($this->user);
        $this->userCountry->setCountry($this->country);
        $this->userCountry->setActive(true);

        $this->countries = [
                                                "ES"
                                                
  
                            ];

        $this->countriesVo = new CountriesVo($this->countries);
        $this->userUuidVo = new UserUuid($this->userUuid);

    }

    public function testPostUserAddAllowedCountriesSuccess(): void
    {
    
        $this->userRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->userUuidVo->value())
            ->willReturn($this->user);

        $this->countryRepository->expects($this->once())
            ->method('findByCode')
            ->with($this->countriesVo->getValues()[0]->value())
            ->willReturn($this->country);

        $this->countryUserRepository->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($userCountry) {
                $this->assertEquals($this->user, $userCountry->getUser());
                $this->assertEquals($this->country, $userCountry->getCountry());
                $this->assertTrue($userCountry->isActive());
            });

        $this->userAddAllowedCountriesService->__invoke($this->countriesVo, $this->userUuidVo);
        
    }
    //     php bin/phpunit tests/User/Application/Service/PostAddCoutriesAllowToUserTest.php

    public function testPostUserNotFound(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->userUuidVo->value())
            ->willReturn(null);

        $this->expectException(UserNoFound::class);

        $this->userAddAllowedCountriesService->__invoke($this->countriesVo, $this->userUuidVo);
        
    }

    public function testPostCountryNotFound(): void
    {
    
        $this->userRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->userUuidVo->value())
            ->willReturn($this->user);

        $this->countryRepository->expects($this->once())
            ->method('findByCode')
            ->with($this->countriesVo->getValues()[0]->value())
            ->willReturn(null);

        $this->expectException(CountryNoFound::class);

        $this->userAddAllowedCountriesService->__invoke($this->countriesVo, $this->userUuidVo);
        
    }


}
