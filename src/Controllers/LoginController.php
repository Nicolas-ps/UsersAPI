<?php

namespace Nicolasps\UsersAPI\Controllers;

use Nicolasps\UsersAPI\Meta\HTTPResponseCodes;
use Nicolasps\UsersAPI\Meta\Request;
use Nicolasps\UsersAPI\Model\Session;
use Nicolasps\UsersAPI\Model\User;
use Nicolasps\UsersAPI\Repositories\SessionRepository;
use Nicolasps\UsersAPI\Repositories\UserRepository;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $userData = json_decode($request->json());
        $email = filter_var($userData->email, FILTER_VALIDATE_EMAIL);
        $password = htmlspecialchars($userData->password);

        $userRepository = new UserRepository(new User());
        $user = $userRepository->getUserByEmail($email);

        if (empty($user)) {
            response([
                'success' => false,
                'message' => 'O usuário não existe'
            ], HTTPResponseCodes::NotFound);
        }

        $passwordIsValid = password_verify($password, $user->password);

        if (! $passwordIsValid) {
            response([
                'success' => false,
                'message' => 'A senha digitada é inválida!'
            ], HTTPResponseCodes::BadRequest);
        }

        $session = (new SessionRepository(new Session()))->new([
            $user->id,
            uniqid('', true),
            (new \DateTime('now'))->add(new \DateInterval('PT60M'))->format('Y-m-d H:i:s')
        ]);

        if ($session) {
            response([
                'success' => true,
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'username' => $user->username,
                    'drinkcounter' => 25,
                    'access_token' => $session->session_id,
                    'expires_in' => 3600
                ]
            ], HTTPResponseCodes::Ok);
        }

    }
}