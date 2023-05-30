<?php

namespace Nicolasps\UsersAPI;

use Nicolasps\UsersAPI\Model\Session;
use Nicolasps\UsersAPI\Model\User;
use Nicolasps\UsersAPI\Repositories\SessionRepository;
use Nicolasps\UsersAPI\Repositories\UserRepository;

class AuthorizationService
{
    public static function isLogged(string $token): bool
    {
        $sessionRepository = new SessionRepository(new Session());

        $session = $sessionRepository->getBySessionId($token);

        return date('Y-m-d H:i:s') < $session->expires_in;
    }
}