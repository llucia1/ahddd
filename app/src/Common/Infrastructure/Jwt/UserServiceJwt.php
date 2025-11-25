<?php
declare(strict_types=1);
namespace GridCP\Common\Infrastructure\Jwt;


use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function PHPUnit\Framework\isNull;

readonly class UserServiceJwt
{

    public function  __construct(private TokenStorageInterface $storage){
    }

    public  function getCurrentUser(): ?UserInterface
    {
        $token = $this->storage->getToken();
        if (!is_null($token))
            $user = $token->getUser();
        else
            $user=null;
        return $user;
    }

    public function getRoles():?array{
        $token = $this->storage->getToken();
        if (!is_null($token)) {
            $user = $token->getUser();
            $roles = $user->getRoles();
        }
        else
            $roles=null;
        return $roles;
    }

    public function isAdmin():?bool{
        $token = $this->storage->getToken();
        if (!is_null($token)) {
            $user = $token->getUser();
            $roles = $user->getRoles();
            return in_array('ROLE_ADMIN', $roles, true);
        }
        else
            $roles=null;
        return $roles;

    }


}