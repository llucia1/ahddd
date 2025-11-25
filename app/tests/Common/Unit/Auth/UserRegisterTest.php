<?php

declare(strict_types=1);

namespace GridCP\Tests\Common\Unit\Auth;

use GridCP\Security\Application\Services\AuthRegisterService;
use GridCP\Security\Infrastructure\DB\MySQL\Entity\AuthEntity;
use GridCP\Security\Infrastructure\DB\MySQL\Repository\AuthRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegisterTest
{
    private $userRepository;
    private $passwordHasher;
    private $userRegisterService;

    public function testCreateUserSuccessfully()
    {
        $email = 'test@test.com';
        $password = 'test123';
        $hashedPassword = 'hashedPassword';

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn(null);

        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->willReturn($hashedPassword);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (AuthEntity $user) use ($email, $hashedPassword) {
                $this->assertSame($email, $user->getEmail(), 'Email does not match');
                $this->assertSame($hashedPassword, $user->getPassword(), 'Hashed password does not match');
            });

        $uuid = $this->userRegisterService->createUser($email, $password);

        $this->assertNotEmpty($uuid, 'UUID is empty');
    }

    public function testCreateUserConflict()
    {
        $email = 'test@test.com';
        $password = 'test123';

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new AuthEntity());

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('AuthEntity already exists');
        $this->userRegisterService->createUser($email, $password);
    }

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(AuthRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userRegisterService = new AuthRegisterService($this->userRepository, $this->passwordHasher);
    }
}
