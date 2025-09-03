<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UsersService;
use App\Transformers\UsersTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
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

    public function store(LoginRequest $request): JsonResponse
    {
        $request->ensureIsNotRateLimited();

        $user = $this->usersService->getByEmail($request->input('email'));

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($request->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($request->throttleKey());

        $token = $user->createToken('wp-backend')->plainTextToken;

        $userData = $this->usersTransformer->transform($user);

        $userData['token'] = $token;

        return response()->json($userData, 200);
    }

    public function destroy(): JsonResponse
    {
        // Invalidate the current PAT
        request()->user()?->currentAccessToken()?->delete();
        return response()->json([], 204);
    }
}
