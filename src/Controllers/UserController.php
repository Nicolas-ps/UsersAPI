<?php

namespace Nicolasps\UsersAPI\Controllers;

use Nicolasps\UsersAPI\AuthorizationService;
use Nicolasps\UsersAPI\Meta\HTTPResponseCodes;
use Nicolasps\UsersAPI\Meta\Request;
use Nicolasps\UsersAPI\Model\Drink;
use Nicolasps\UsersAPI\Model\User;
use Nicolasps\UsersAPI\Repositories\DrinkRepository;
use Nicolasps\UsersAPI\Repositories\UserRepository;

class UserController extends Controller
{
    public function create(Request $request): void
    {
        $userData = json_decode($request->json());
        $userRepository = new UserRepository(new User());

        $email = filter_var($userData->email, FILTER_VALIDATE_EMAIL);

        if (! $email) {
            response([
                'error' => true,
                'message' => 'Requisição mal formada!'
            ], HTTPResponseCodes::BadRequest);
        }

        $username = htmlspecialchars($userData->username);
        $password = htmlspecialchars($userData->password);

        if (empty($username) || empty($password)) {
            response([
                'error' => true,
                'message' => 'Requisição mal formada!'
            ], HTTPResponseCodes::BadRequest);
        }

        if (! empty($userRepository->getUserByEmail($email))) {
            response([
                'success' => false,
                'message' => 'O usuário já existe na base'
            ], HTTPResponseCodes::Ok);
        }

        $newUser = $userRepository->new([$email, $username, password_hash($password, PASSWORD_DEFAULT)]);

        if (! $newUser) {
            response([
                'error' => true,
                'message' => 'Ocorreu um erro ao cadastrar o usuário!'
            ], HTTPResponseCodes::ServerError);
        }

        response([
            'success' => true,
            'message' => 'Usuário Cadastrado com sucesso!'
        ], HTTPResponseCodes::Ok);
    }

    public function find(Request $request): void
    {
        $authorizationToken = getallheaders()['Authorization'];

        if (empty($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Requisição mal formada!'
            ], HTTPResponseCodes::BadRequest);
        }

        if (! AuthorizationService::isLogged($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Usuário não autorizado!'
            ], HTTPResponseCodes::Unauthorized);
        }

        $params = $request->all();
        $userRepository = new UserRepository(new User());
        $user = $userRepository->getById($params->id);

        if (! $user) {
            response([
                'error' => true,
                'message' => 'Usuário não encontrado!'
            ], HTTPResponseCodes::NotFound);
        }

        response([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'username' => $user->username,
                'drink_counter' => $userRepository->drinkCount($user->id) ?? 0
            ]
        ], HTTPResponseCodes::Ok);
    }

    public function all(Request $request): void
    {
        $authorizationToken = getallheaders()['Authorization'];

        if (empty($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Requisição mal formada!'
            ], HTTPResponseCodes::BadRequest);
        }

        if (! AuthorizationService::isLogged($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Usuário não autorizado!'
            ], HTTPResponseCodes::Unauthorized);
        }

        $userRepository = new UserRepository(new User());
        $users = $userRepository->AllUsersWithDrinkCount();

        if (! $users) {
            echo json_encode([
                'error' => true,
                'message' => 'Ocorreu um erro ao listar os usuários!'
            ]);

            header('HTTP/1.0 500 Server Error');
            exit();
        }

        echo json_encode([
            'success' => true,
            'data' => $users
        ]);

        header('HTTP/1.0 200 Ok');
        exit();
    }

    public function consumes(Request $request): void
    {
        $authorizationToken = getallheaders()['Authorization'];

        if (empty($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Requisição mal formada!'
            ], HTTPResponseCodes::BadRequest);
        }

        if (! AuthorizationService::isLogged($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Usuário não autorizado!'
            ], HTTPResponseCodes::Unauthorized);
        }

        $input = json_decode($request->json());
        $data = $request->all();
        $userRepository = new UserRepository(new User());
        $drinkRepository = new DrinkRepository(new Drink());

        $user = $userRepository->getById($data->id);
        $drink = $drinkRepository->getById($input->drink_id);

        if (! $drink) {
            response([
                'error' => true,
                'message' => 'Esse drink não existe no catálogo!'
            ], HTTPResponseCodes::NotFound);
        }

        if (empty($user)) {
            response([
                'error' => true,
                'message' => 'Usuário não encontrado!'
            ], HTTPResponseCodes::NotFound);
        }

        if (! $userRepository->consumeDrink($drink->id, $user->id)) {
            response([
                'error' => true,
                'message' => 'Ocorreu um erro ao computar o drink consumido pelo usuário!'
            ], HTTPResponseCodes::ServerError);
        }

        response([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->username,
                'drinkcount' => $userRepository->drinkCount($data->id) ?? 0
            ]
        ], HTTPResponseCodes::Ok);
    }

    public function delete(Request $request): void
    {
        $authorizationToken = getallheaders()['Authorization'];

        if (empty($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Requisição mal formada!'
            ], HTTPResponseCodes::BadRequest);
        }

        if (! AuthorizationService::isLogged($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Usuário não autorizado!'
            ], HTTPResponseCodes::Unauthorized);
        }

        $data = $request->all();
        $userRepository = new UserRepository(new User());
        $user = $userRepository->getById($data->id);

        if (! $user) {
            response([
                'error' => true,
                'message' => 'Usuário não encontrado!'
            ], HTTPResponseCodes::NotFound);
        }

        if (! $userRepository->delete($user->id)) {
            response([
                'error' => true,
                'message' => 'Ocorreu um erro ao deletar o usuário!'
            ], HTTPResponseCodes::ServerError);
        }

        response([
            'success' => true,
            'message' => 'O Usuário foi deletado!'
        ], HTTPResponseCodes::Ok);
    }

    public function edit(Request $request): void
    {
        $authorizationToken = getallheaders()['Authorization'];

        if (empty($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Requisição mal formada!'
            ], HTTPResponseCodes::BadRequest);
        }

        if (! AuthorizationService::isLogged($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Usuário não autorizado!'
            ], HTTPResponseCodes::Unauthorized);
        }

        $data = $request->all();
        $userRepository = new UserRepository(new User());
        $user = $userRepository->getById($data->id);

        if (! $user) {
            response([
                'error' => true,
                'message' => 'Usuário não encontrado!'
            ], HTTPResponseCodes::NotFound);
        }

        $json = json_decode($request->json());
        $userUpdated = $userRepository->update([
            $json->email ?? $user->email,
            $json->username ?? $user->username,
            password_hash($json->password, PASSWORD_DEFAULT) ?? $user->password
        ], $user->id);

        if (! $userUpdated) {
            response([
                'error' => true,
                'message' => 'Erro ao editar os dados do usuário!'
            ], HTTPResponseCodes::ServerError);
        }

        response([], HTTPResponseCodes::NoContent);
    }

    public function userRegistration(Request $request): void
    {
        $authorizationToken = getallheaders()['Authorization'];

        if (empty($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Requisição mal formada!'
            ], HTTPResponseCodes::BadRequest);
        }

        if (! AuthorizationService::isLogged($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Usuário não autorizado!'
            ], HTTPResponseCodes::Unauthorized);
        }

        $userRepository = new UserRepository(new User());

        response([
            'success' => true,
            'data' => $userRepository->userRegistrationPerDay() ?? []
        ], HTTPResponseCodes::Ok);
    }

    public function consummationRanking(Request $request)
    {
        $authorizationToken = getallheaders()['Authorization'];

        if (empty($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Requisição mal formada!'
            ], HTTPResponseCodes::BadRequest);
        }

        if (! AuthorizationService::isLogged($authorizationToken)) {
            response([
                'error' => true,
                'message' => 'Usuário não autorizado!'
            ], HTTPResponseCodes::Unauthorized);
        }

        $from = json_decode($request->json())->from ?? '';
        $to = json_decode($request->json())->to ?? '';

        $userRepository = new UserRepository(new User());
        $consummation = parseToArray($userRepository->userConsummation($from, $to));

        $consummationRanking = array_group_by($consummation, 'date');
        ksort($consummationRanking);

        $return = [];
        foreach ($consummationRanking as $key => $value) {
            $return[$key] = [
                'user_id' => $value[0]['id'],
                'username' => $value[0]['username'],
                'drinkcount' => $value[0]['drinkcount']
            ];
        }

        response([
            'success' => true,
            'data' => $return
        ], HTTPResponseCodes::Ok);
    }
}