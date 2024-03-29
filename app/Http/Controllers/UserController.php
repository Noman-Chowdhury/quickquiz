<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserRole;
use Laravel\Sanctum\Exceptions\MissingAbilityException;

class UserController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $creds = $request->validate([
            'password' => ['required', Password::min(8)->uncompromised()],
            'name' => 'nullable|string',
            'phone_number'=> ['required','numeric','digits:11','regex:/^(?:\+?88)?01[3-9]\d{8}$/', 'unique:users,phone_number'],
        ]);

        $user = User::where('phone_number', $creds['phone_number'])->first();
        if ($user) {
            return response(['error' => 1, 'message' => 'user already exists'], 409);
        }

        $user = User::create([
            'phone_number' => $creds['phone_number'],
            'password' => Hash::make($creds['password']),
            'name' => $creds['name']
        ]);

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => config('hydra.default_user_role_id', 2)
        ]);


        return $user;
    }

    /**
     * Authenticate an user and dispatch token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {
        $creds = $request->validate([
            'phone_number' => ['required', 'exists:users,phone_number'],
            'password' => 'required',
        ]);

        $user = User::where('phone_number', $creds['phone_number'])->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['error' => 1, 'message' => 'invalid credentials'], 401);
        }

        if (config('hydra.delete_previous_access_tokens_on_login', false)) {
            $user->tokens()->delete();
        }


        $roles =  $user->roles()->get();
        $_roles = [];
        foreach ($roles as $role) {
            $_roles[] = $role->slug;
        }

        $plainTextToken = $user->createToken('hydra-api-token', $_roles)->plainTextToken;
        return response(['error' => 0, 'id' => $user->id, 'token' => $plainTextToken , 'role'=>$user->roles], 200);
    }

    public function show(User $user) {
        return $user;
    }

    /**
     * @throws MissingAbilityException
     */
    public function update(Request $request, User $user) {
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->password = $request->password ?  Hash::make($request->password) : $user->password;
        $user->email_verified_at = $request->email_verified_at ?? $user->email_verified_at;

        //check if the logged-in user is updating it's own record


        $loggedInUser = $request->user();
        if ($loggedInUser->id == $user->id) {
            $user->update();
        } else if ($loggedInUser->tokenCan('admin') || $loggedInUser->tokenCan('super-admin')) {
            $user->update();
        } else {
            throw new MissingAbilityException("Not Authorized");
        }

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {

        $adminRole = Role::where('slug', 'admin')->first();
        $userRoles = $user->roles;

        if ($userRoles->contains($adminRole)) {
            //the current user is admin, then if there is only one admin - don't delete
            $numberOfAdmins =  Role::where('slug', 'admin')->first()->users()->count();
            if (1 == $numberOfAdmins) {
                return response(['error' => 1, 'message' => 'Create another admin before deleting this only admin user'], 409);
            }
        }

        $user->delete();

        return response(['error' => 0, 'message' => 'user deleted']);
    }

    public function me(Request $request) {
        $user =  $request->user();
        $user->role = $user->currentAccessToken()->abilities[0];
        return $user;
    }
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return [
            'message'=>'Successfully Logged out'
        ];
    }
}
