<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api');
    }
    
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password')); 
        $user->save();

        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {        
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        //$user->password = bcrypt($request->input('password')); 

        $user->save();

        return new UserResource($user);
    }

    public function updatePassword(Request $request, User $user)
    {
        $user->password = bcrypt($request->input('newPassword'));
        $user->save();
        return response()->json([
            'message' => 'Password updated successfully.',
            'user' => new UserResource($user),
        ], Response::HTTP_OK);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }

}
