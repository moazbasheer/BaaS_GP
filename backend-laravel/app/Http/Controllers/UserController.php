<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Validator;
use Auth;
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="L5 OpenApi",
 *      description="L5 Swagger OpenApi description",
 *      @OA\Contact(
 *          email="darius@matulionis.lt"
 *      ),
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="registerNewUser",
     *      tags={"Authentication"},
     *      summary="register new user",
     *      description="register new user",
     *      @OA\Parameter(
     *          name="name",
     *          description="username",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="email",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="password",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="role_name",
     *          description="role_name",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns status of registration.
     */
    function register(Request $req) {
        // use validator.
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role_name' => 'required'
        ]);
        $message = [];
        $message = format_message($message, $validator);
        if($role_name === "organization") {
            // use validator.
        } elseif($role_name === "client") {
            // use validator.
        } else {
            // add to the message. (invalid role_name)
            // either client or organization.
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
        // $user_id = User::latest()->first()->id;
        return response(json_encode([
            'status' => true,
            'message' => ['user registered successfully!']
        ]), 200);
    }
    /**
     * the function re-formats the message and add it to 
     * the old message and returns the result.
     */
    function format_message($old_message, $validator) {
        $message = $old_message;
        $messages = json_decode($validator->messages());
        foreach($messages as $key => $value) {
            foreach($value as $x) {
                $message[] = $x;
            }
        }
        return $message;
    }
    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="loginUser",
     *      tags={"Authentication"},
     *      summary="log in user",
     *      description="log in user",
     *      @OA\Parameter(
     *          name="email",
     *          description="email",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="password",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns status of login.
     */
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
            return response(json_encode([
                'status' => false,
                'message' => $message
            ]), 400);
        }
        $user = User::where('email', $req->email)->first();
    
        if (! $user || ! Hash::check($req->password, $user->password)) {
            return response(json_encode([
                'status' => false,
                'message' => ['Unauthenticated']
            ]), 401);
        }
        $token = $user->createToken('my-app')->plainTextToken;
        return response(json_encode([
            'status' => true,
            'message' => 'user logged in successfully!',
            'user' => $user,
            'token' => $token
        ]), 200);
    }
    /**
     * @OA\Post(
     *      path="/api/logout",
     *      operationId="logoutUser",
     *      tags={"Authentication"},
     *      summary="log out user",
     *      description="log out user",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns status of logout.
     */
    function logout() {
        Auth::user()->currentAccessToken()->delete();
        return response(json_encode([
            'status' => true,
            'message' => 'user logged out successfully!'
        ]), 200);
    }
    /**
     * @OA\Get(
     *      path="/api/user",
     *      operationId="UserInfo",
     *      tags={"User"},
     *      summary="get user info",
     *      description="get user info",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns status of logout.
     */
    function user_info() {
        return response(json_encode(Auth::user()), 200);
    }
}
