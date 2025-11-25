<?php
declare(strict_types=1);

namespace Proxmox\Version\Application\Service;

use Faker\Generator;

use PHPUnit\Framework\TestCase;

use GridCP\Security\Common\Infrastructure\DB\MySQL\Entity\AuthEntity;

use GridCP\User\Application\Response\UserMeResponse;
use GridCP\User\Application\Service\GetUserMeService;
use GridCP\User\Domain\Exception\GetUserMeError;
use Psr\Log\LoggerInterface;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserEntity;
use GridCP\User\Domain\Repository\IUserRepository;

class GetUserServiceTest extends TestCase
{
    protected Generator $faker;

    private $userRepositoryMock;
    private $loggerMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->userRepositoryMock = $this->createMock(IUserRepository::class);
    }

    
    public function testGetUserMe()
    {

        $authEntity = new AuthEntity();
        $authId = 2;
        $authEntity->setId($authId);
        $authEntity->setEmail("user2@email.es");
        $authEntity->setUsername("user2");
        $authEntity->setPassword("123456");


        $userEntity = new UserEntity();
        $userEntity->setId($authId);
        $userEntity->setEmail( $authEntity->getEmail());
        $userEntity->setFirstName("Jose2");
        $userEntity->setLastName("Llucia2");

        

        


        $userMeMock = new UserMeResponse(
                                            $userEntity->getEmail(),
                                            $userEntity->getFirstName(),
                                            $userEntity->getLastName()
                                        );

        $this->userRepositoryMock->expects($this->once())
                                                        ->method('findByid')
                                                        ->with(2)
                                                        ->willReturn($userEntity);

                               
        $getUserService = new GetUserMeService ( $this->userRepositoryMock, $this->loggerMock);


        $response = $getUserService->__invoke($authEntity);
        $responseMock = $getUserService->toResponse( $userMeMock );


        $this->assertEquals($response, $responseMock);           
        $this->assertIsArray($response);
        
        $this->assertArrayHasKey('email', $response);
        $this->assertArrayHasKey('firstName', $response);
        $this->assertArrayHasKey('lastName', $response);      
        
        $this->assertEquals('user2@email.es', $response['email']);
        $this->assertEquals('Jose2', $response['firstName']);
        $this->assertEquals('Llucia2', $response['lastName']);
    }
    
    public function testUserMeConflict()
    {
        $authEntity = new AuthEntity();
        $authId = 2;
        $authEntity->setId($authId);
        $authEntity->setEmail("user2@email.es");
        $authEntity->setUsername("user2");
        $authEntity->setPassword("123456");


        $this->userRepositoryMock->expects($this->once())
                                                        ->method('findByid')
                                                        ->with(2)
                                                        ->willReturn(null);

        try {
            $getUserService = new GetUserMeService($this->userRepositoryMock, $this->loggerMock);
            $getUserService->__invoke($authEntity);
        } catch (GetUserMeError $e) {
            $this->assertSame('Not Found User/me.', $e->getMessage());
            return;
        }

        $this->fail('Expected GetUserMeError was not thrown.');
        
    }
    
}