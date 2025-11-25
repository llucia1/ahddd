<?php
declare(strict_types=1);

namespace Proxmox\Version\Application\Service;

use GridCP\User\Domain\Exception\UserNoFound;
use GridCP\User\Domain\Repository\IUserIp4sRepository;
use GridCP\User\Domain\Repository\IUserRepository;
use GridCP\User\Domain\VO\UserUuid;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserEntity;
use PHPUnit\Framework\TestCase;

use Psr\Log\LoggerInterface;
use Faker\Factory as FakerFactory;
use GridCP\User\Application\Response\Ip4Response;
use GridCP\User\Application\Response\Ip4sResponse;
use GridCP\User\Application\Service\GetUserAddAllowedIp4sService;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserIp4sEntity;

class GetAddIp4sAllowToUserTest extends TestCase
{
    const NULL = null;

    protected IUserRepository $userRepository;
    protected IUserIp4sRepository $userIp4sRepository;

    private GetUserAddAllowedIp4sService $userAddAllowedIp4sService;

    private UserUuid $userUuid;
    private array $ips;
    private array $ipsEntity;
    private UserEntity $user;
    private Ip4sResponse $reponse;
    private mixed $faker;

    public function setUp(): void
    {

        $this->userRepository = $this->getMockBuilder( IUserRepository::class )->disableOriginalConstructor()->getMock();
        $this->userIp4sRepository = $this->getMockBuilder( IUserIp4sRepository::class )->disableOriginalConstructor()->getMock();
        $loggerMock = $this->createMock(LoggerInterface::class);



        $this->userAddAllowedIp4sService = new GetUserAddAllowedIp4sService($this->userRepository, $this->userIp4sRepository,$loggerMock);




        $userUuid = 'ae901ebb-656f-44ff-b7d4-80bae65629c2';
        $this->userUuid = new UserUuid($userUuid);

        $this->user = new UserEntity();
        $this->user->setId(1);
        $this->user->setUuid($this->userUuid->value());
        $this->user->setEmail('xavi@xavi.com');
        $this->user->setFirstName('firstName');
        $this->user->setLastName('lastName');
        $this->user->setUuidAuth('dd1f24cb-3923-4117-9a50-1ece75d06000');

        $this->faker = FakerFactory::create();

        $this->ips = [
                                "192.0.2.0",
                                "192.0.2.1"
                            ];
                            
        $ip1 = new UserIp4sEntity();
        $ip1->setIp($this->ips[0]);
        $ip1->setUserId($this->user->getId());
        $ip1->setActive(true);

        $this->ipsEntity = [
            $ip1
                    ];


        $this->reponse  = new Ip4sResponse( new Ip4Response($this->ips[0]) );
    }
    public function testGetUserAddAllowedIp4sSuccess(): void
    {

        $this->userRepository->expects($this->once())
                                         ->method('findByUuid')
                                         ->with($this->userUuid->value())
                                         ->willReturn($this->user);

        $this->userIp4sRepository->expects($this->once())
                                 ->method('findByUserId')
                                 ->with($this->user->getId())
                                 ->willReturn($this->ipsEntity);

        $response = $this->userAddAllowedIp4sService->__invoke($this->userUuid);
        $this->assertEquals($response, $this->reponse);
        
    }
    public function testPostUserNotFound(): void
    {

        $this->userRepository->expects($this->once())
                                         ->method('findByUuid')
                                         ->with($this->userUuid->value())
                                         ->willReturn(self::NULL);

        $this->expectException(UserNoFound::class);

        $this->userAddAllowedIp4sService->__invoke($this->userUuid);
        
    }
    //     php bin/phpunit tests/User/Application/Service/GetAddIp4sAllowToUserTest.php
}
