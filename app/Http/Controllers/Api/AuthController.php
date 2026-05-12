<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string'
        ];

        $messages = [
            'email.required' => 'Email is required',
            'email.email' => 'Email is not valid',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return ApiMessage::error($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiMessage::error('Error', 'User or Password is incorrect', 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return ApiMessage::error('Error', 'User or Password is incorrect', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $data = [
            'user' => $user,
            'token' => $token,
        ];

        return ApiMessage::success('Login successful', $data, 200);
    }

    public function registration(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|same:password',
                'role_id' => 'required|exits:role,id',
            ];

            $messages = [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Email must be a valid email address',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
                'password_confirmation.required' => 'Password confirmation is required',
                'password_confirmation.same' => 'Password confirmation must be matched with password',
                'role_id.required' => 'Role is required',
                'role_id.exists' => 'Role not found',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return ApiMessage::error($validator->errors(), 400);
            }

            DB::beginTransaction();

            try {
                //save ke tabel user 
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->role_id = $request->role_id;
                $user->save();

                DB::commit();
                return ApiMessage::success('Success', 'Registration successful', 201);
            } catch (\Throwable $th) {
                DB::rollBack();
                return ApiMessage::error('Error', $th->getMessage(), 500);
            }
        } catch (\Throwable $th) {
            return ApiMessage::error('Error', $th->getMessage(), 500);
        }
    }

    public function index()
    {
        try {
            $users = User::all();
            return ApiMessage::success('Success get data', $users, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }
}
