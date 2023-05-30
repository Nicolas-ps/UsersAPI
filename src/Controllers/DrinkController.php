<?php

namespace Nicolasps\UsersAPI\Controllers;

use Nicolasps\UsersAPI\Meta\HTTPResponseCodes;
use Nicolasps\UsersAPI\Meta\Request;
use Nicolasps\UsersAPI\Model\Drink;
use Nicolasps\UsersAPI\Repositories\DrinkRepository;

class DrinkController extends Controller
{
    public function all(Request $request)
    {
        $drinkRepository = new DrinkRepository(new Drink());
        $drinks = $drinkRepository->all();

        if (! $drinks) {
            response([
                'error' => true,
                'message' => 'Ocorreu um erro ao recuperar o catálogo de drinks!'
            ], HTTPResponseCodes::ServerError);
        }

        response([
            'success' => true,
            'data' => $drinks
        ], HTTPResponseCodes::Ok);
    }
}