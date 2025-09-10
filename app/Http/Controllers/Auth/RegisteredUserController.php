<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Services\UsersService;
use App\Transformers\UsersTransformer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /** @var \App\Services\UsersService */
    private $usersService;

    /** @var UsersTransformer */
    private $usersTransformer;

    public function __construct(
        UsersService $usersService,
        UsersTransformer $usersTransformer
    ) {
        $this->usersService = $usersService;
        $this->usersTransformer = $usersTransformer;
    }

    public function store(RegisterRequest $request)
    {
        $user = $this->usersService->create($request->getUserData());

        event(new Registered($user));

        $token = $user->createToken('wp-backend')->plainTextToken;


        $token = $user->createToken('wp-backend')->plainTextToken;

        $data = $this->usersTransformer->transform($user);
        $data['token'] = $token;

        return response()->json($data, 201);
    }
}
