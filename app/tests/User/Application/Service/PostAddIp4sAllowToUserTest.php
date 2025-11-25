<?php
declare(strict_types=1);

namespace Proxmox\Version\Application\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Response\Ip4sNotExitsResponses;
use GridCP\User\Application\Service\PostUserAddAllowedIp4sService;
use GridCP\User\Domain\Exception\UserNoFound;
use GridCP\User\Domain\Repository\IUserIp4sRepository;
use GridCP\User\Domain\Repository\IUserRepository;
use GridCP\User\Domain\VO\Ip4Ips;
use GridCP\User\Domain\VO\UserUuid;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserEntity;
use PHPUnit\Framework\TestCase;

use Psr\Log\LoggerInterface;
use Faker\Factory as FakerFactory;



class PostAddIp4sAllowToUserTest extends TestCase
{
    const NULL = null;

    protected IUserRepository $userRepository;
    protected IUserIp4sRepository $userIp4sRepository;

    private PostUserAddAllowedIp4sService $userAddAllowedIp4sService;

    private UserUuid $userUuid;
    private Ip4Ips $ipsVo;
    private UserEntity $user;

    public function setUp(): void
    {

        $this->userRepository = $this->getMockBuilder( IUserRepository::class )->disableOriginalConstructor()->getMock();
        $this->userIp4sRepository = $this->getMockBuilder( IUserIp4sRepository::class )->disableOriginalConstructor()->getMock();
        $loggerMock = $this->createMock(LoggerInterface::class);



        $this->userAddAllowedIp4sService = new PostUserAddAllowedIp4sService($this->userRepository, $this->userIp4sRepository,$loggerMock );




        $userUuid = 'ae901ebb-656f-44ff-b7d4-80bae65629c2';
        $this->userUuid = new UserUuid($userUuid);

        $this->user = new UserEntity();
        $this->user->setId(1);
        $this->user->setUuid($this->userUuid->value());
        $this->user->setEmail('xavi@xavi.com');
        $this->user->setFirstName('firstName');
        $this->user->setLastName('lastName');
        $this->user->setUuidAuth('dd1f24cb-3923-4117-9a50-1ece75d06000');

        $ips = [
                                "192.0.2.0",
                                "192.0.2.1"
                            ];
        $this->ipsVo = new Ip4Ips($ips);

    }
    public function testPostUserAddAllowedIp4sSuccess(): void
    {

        $this->userRepository->expects($this->once())
                                         ->method('findByUuid')
                                         ->with($this->userUuid->value())
                                         ->willReturn($this->user);



        $this->userIp4sRepository->expects($this->once())
                                 ->method('findByUserId')
                                 ->with($this->user->getId())
                                 ->willReturn([]);

        $response = $this->userAddAllowedIp4sService->__invoke($this->ipsVo , $this->userUuid);
        $this->assertEquals($response, $this->ipsVo->get());
        
    }
    public function testPostUserNotFound(): void
    {

        $this->userRepository->expects($this->once())
                                         ->method('findByUuid')
                                         ->with($this->userUuid->value())
                                         ->willReturn(self::NULL);

        $this->expectException(UserNoFound::class);

        $this->userAddAllowedIp4sService->__invoke($this->ipsVo , $this->userUuid);
        
    }
    //     php bin/phpunit tests/User/Application/Service/PostAddIp4sAllowToUserTest.php
    

}
