<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Validator;
use Auth;
class UserController extends Controller
{
    function register(Request $req) {
        // use validator.
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role_name' => 'required'
        ]);
        $message = [];
        $messages = json_decode($validator->messages());
        foreach($messages as $key => $value) {
            foreach($value as $x) {
                $message[] = $x;
            }
        }
        if($validator->fails()) {
            return response(json_encode([
                'status' => false,
                'message' => $message
            ]), 400);
        }
        User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'role_name' => $req->role_name
        ]);
        return response(json_encode([
            'status' => true,
            'message' => ['user registered successfully!']
        ]), 200);
    } 
    function login(Request $req) {
        $validator = Validator::make($req->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);
        $message = [];
        $messages = json_decode($validator->messages());
        foreach($messages as $key => $value) {
            foreach($value as $x) {
                $message[] = $x;
            }
        }
        if($validator->fails()) {
            return response(400,json_encode([
                'status' => false,
                'message' => $message
            ]));
        }
        $user = User::where('email', $req->email)->first();
    
        if (! $user || ! Hash::check($req->password, $user->password)) {
            return response(401, json_encode([
                'status' => false,
                'message' => ['Unauthenticated']
            ]));
        }
        $token = $user->createToken('my-app')->plainTextToken;
        return response(json_encode([
            'status' => true,
            'message' => 'user logged in successfully!',
            'user' => $user,
            'token' => $token
        ]), 200);
    }
    function logout() {
        Auth::user()->currentAccessToken()->delete();
        return response(json_encode([
            'status' => true,
            'message' => 'user logged out successfully!'
        ]), 200);
    }
}
