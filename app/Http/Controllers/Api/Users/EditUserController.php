<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EditRequest;
use App\Services\UsersService;
use App\Transformers\UsersTransformer;


class EditUserController extends Controller
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

    public function update(EditRequest $request)
    {
        $user = $request->user();

        $this->usersService->update($user, $request->getUserData());

        $userData = $this->usersTransformer->transform($user);

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $userData
        ], 200);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['errors' => ['current_password' => 'current_password_incorrect']], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'message' => 'Password updated successfully',
        ], 200);
    }
}
