<?php
declare(strict_types=1);

namespace Proxmox\Version\Application\Service;

use GridCP\User\Application\Service\GetUserAllowedCountriesService;
use GridCP\User\Domain\Exception\UserNoFound;
use GridCP\User\Domain\Repository\IUserRepository;
use GridCP\User\Domain\VO\UserUuid;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserEntity;
use PHPUnit\Framework\TestCase;

use Psr\Log\LoggerInterface;
use Faker\Factory as FakerFactory;
use GridCP\User\Application\Response\CountryResponse;
use GridCP\User\Application\Response\CountriesResponse;
use GridCP\User\Domain\Repository\IUserCountriesRepository;
use GridCP\User\Infrastructure\DB\MySQL\Entity\CountryEntity;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserCountriesEntity;

class GetCountriesAllowToUserTest extends TestCase
{
    const NULL = null;

    protected IUserRepository $userRepository;
    protected IUserCountriesRepository $userCountriesRepository;

    private GetUserAllowedCountriesService $userAllowedCountriesService;

    private UserUuid $userUuid;
    private array $countryEntity;
    private UserEntity $user;
    private CountryEntity $country;
    private CountriesResponse $reponse;
    private mixed $faker;

    public function setUp(): void
    {

        $this->userRepository = $this->getMockBuilder( IUserRepository::class )->disableOriginalConstructor()->getMock();
        $this->userCountriesRepository = $this->getMockBuilder( IUserCountriesRepository::class )->disableOriginalConstructor()->getMock();
        $loggerMock = $this->createMock(LoggerInterface::class);



        $this->userAllowedCountriesService = new GetUserAllowedCountriesService($this->userRepository, $this->userCountriesRepository,$loggerMock);




        $userUuid = 'ae901ebb-656f-44ff-b7d4-80bae65629c2';
        $this->userUuid = new UserUuid($userUuid);

        $this->country = new CountryEntity();
        $this->country->setId(1);
        $this->country->setName('Spain');
        $this->country->setCode('ES');
        $this->country->setCode3('ESP');
        

        $this->user = new UserEntity();
        $this->user->setId(1);
        $this->user->setUuid($this->userUuid->value());
        $this->user->setEmail('xavi@xavi.com');
        $this->user->setFirstName('firstName');
        $this->user->setLastName('lastName');
        $this->user->setUuidAuth('dd1f24cb-3923-4117-9a50-1ece75d06000');

        $this->faker = FakerFactory::create();
                            
        $userCountryEntity = new UserCountriesEntity();
        $userCountryEntity->setCountry($this->country);
        $userCountryEntity->setUser($this->user);
        $userCountryEntity->setActive(true);

        $this->countryEntity = [
            $userCountryEntity
                    ];


        $this->reponse  = new CountriesResponse( new CountryResponse($this->country->getCode() ) );
    }
    public function testGetUserAddAllowedIp4sSuccess(): void
    {

        $this->userRepository->expects($this->once())
                                         ->method('findByUuid')
                                         ->with($this->userUuid->value())
                                         ->willReturn($this->user);

        $this->userCountriesRepository->expects($this->once())
                                 ->method('findByUserId')
                                 ->with($this->user->getId())
                                 ->willReturn($this->countryEntity);

        $response = $this->userAllowedCountriesService->__invoke($this->userUuid);
        $this->assertEquals($response, $this->reponse);
        
    }
    public function testPostUserNotFound(): void
    {

        $this->userRepository->expects($this->once())
                                         ->method('findByUuid')
                                         ->with($this->userUuid->value())
                                         ->willReturn(self::NULL);

        $this->expectException(UserNoFound::class);

        $this->userAllowedCountriesService->__invoke($this->userUuid);
        
    }
    //     php bin/phpunit tests/User/Application/Service/GetCountriesAllowToUserTest.php
}
