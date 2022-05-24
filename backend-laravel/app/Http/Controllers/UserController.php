<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Organization;
use Hash;
use Validator;
use Auth;
use Response;
use Spatie\Permission\Models\Role;
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
     *          description="role name",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="first_name",
     *          description="first name (for client)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="last_name",
     *          description="last name (for client)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="username",
     *          description="username (for client)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="phone_number",
     *          description="phone number (for client)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *      ),
     *      @OA\Response(response=400, description="Bad request"),
     *      security={
     *          {"api_key_security_example": {}}
     *      }
     * )
     *
     * Returns status of registration.
     */
    function register(Request $req) {
        // use validator.
        $validator = Validator::make($req->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role_name' => 'required'
        ]);
        $is_failed = false;
        $message = [];
        if($validator->fails()) {
            $is_failed = true;
            $message = $this->format_message($message, $validator);
        }
        $role_name = $req->role_name;
        if($role_name === "organization") {
            // use validator.
            $validator = Validator::make($req->all(), [
                'name' => 'required',
                'phone_number' => 'required|min:11',
                'postal_code' => 'required|size:5',
                'address' => 'required'
            ]);
            if($is_failed || $validator->fails()) {
                $is_failed = true;
                $message = $this->format_message($message, $validator);
            } else {
                $user = User::create([
                    'email' => $req->email,
                    'password' => Hash::make($req->password),
                    'role_name' => $req->role_name
                ]);
                
                Organization::create([
                    'user_id' => User::latest()->first()->id,
                    'name' => $req->name,
                    'postal_code' => $req->postal_code,
                    'address' => $req->address,
                    'phone_number' => $req->phone_number
                ]);
                $user->assignRole(Role::find(1));
            }
        } elseif($role_name === "client") {
            $validator = Validator::make($req->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required',
                'phone_number' => 'required|min:11'
            ]);
            if($is_failed || $validator->fails()) {
                $is_failed = true;
                $message = $this->format_message($message, $validator);
            } else {
                $user = User::create([
                    'email' => $req->email,
                    'password' => Hash::make($req->password),
                    'role_name' => $req->role_name
                ]);
                
                Client::create([
                    'user_id' => User::latest()->first()->id,
                    'first_name' => $req->first_name,
                    'last_name' => $req->last_name,
                    'username' => $req->username,
                    'phone_number' => $req->phone_number
                ]);
                $user->assignRole(Role::find(2));
            }
        } else {
            // add to the message. (invalid role_name)
            // either client or organization.
            
        }
        
        if($is_failed) {
            return response(json_encode([
                'status' => false,
                'message' => $message
            ]), 200);
        }
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
     *          name="emailorusername",
     *          description="email or username",
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
            'emailorusername' => 'required',
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
        $user1 = User::where('email', $req->emailorusername)->first();
        $user2 = null;
        if($req->role_name == "client") {
            $user2 = Client::where('username', $req->emailorusername)->first();
            if($user2) {
                $user2 = User::find($user2->user_id);
            } else {
                $user2 = null;
            }
        }
        $temp = $user1??$user2; //
        if($temp) {
            $user = [
                'email' => $temp->email,
                'password' => $temp->password,
                'role_name' => $temp->role_name
            ];
            
            if($temp->role_name == 'client') {
                $temp1 = Client::where('user_id', $temp->id)->first();
                $user["first_name"] = $temp1->first_name;
                $user["last_name"] = $temp1->last_name;
                $user["username"] = $temp1->username;
                $user["phone_number"] = $temp1->phone_number;
            } elseif($temp->role_name == 'organization') {
                $temp1 = Organization::where('user_id', $temp->id)->first();
                $user["name"] = $temp1->name;
                $user["address"] = $temp1->address;
                $user["postal_code"] = $temp1->postal_code;
                $user["phone_number"] = $temp1->phone_number;
            } elseif($temp->role_name === 'admin') {
                $temp1 = Admin::where('user_id', $temp->id)->first();
                $user["username"] = $temp1->username;
            } // driver and passenger
        }
        if (! $user || ! Hash::check($req->password, $user["password"])) {
            return response(json_encode([
                'status' => false,
                'message' => ['Unauthenticated']
            ]), 200);
        }
        
        $auth_user = $temp;
        if($auth_user) {
            $token = $auth_user->createToken('my-app')->plainTextToken;
        }
        return response(json_encode([
            'status' => true,
            'message' => ['user logged in successfully!'],
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
        $user = Auth::user();
        $message = [
            'email' => $user->email,
            'role_name' => $user->role_name
        ];
        $user_info = null;
        if($user->role_name === "client") {
            $user_info = Client::where('user_id', $user->id)->first();
            $message['profile_image_name'] = $user_info->profile_image_name;
            $message['profile_image_src'] = $user_info->profile_image_src;
            $message['first_name'] = $user_info->first_name;
            $message['last_name'] = $user_info->last_name;
            $message['username'] = $user_info->username;
            $message['phone_number'] = $user_info->phone_number;
            
            return response([
                'status' => true,
                'message' => $message
            ], 200);
        } elseif($user->role_name === "organization") {
            $user_info = Organization::where('user_id', $user->id)->first();
            $message['profile_image_name'] = $user_info->profile_image_name;
            $message['profile_image_src'] = $user_info->profile_image_src;
            $message['name'] = $user_info->name;
            $message['postal_code'] = $user_info->postal_code;
            $message['address'] = $user_info->address;
            $message['phone_number'] = $user_info->phone_number;
            
            return response([
                'status' => true,
                'message' => $message
            ], 200);
        } elseif($user->role_name === "admin") {
            $user_info = Organization::where('user_id', $user->id)->first();
            $message['username'] = $user_info->username;
            return response([
                'status' => true,
                'message' => $message
            ], 200);
        }
        return [
            'status' => 'false',
            'message' => ['role_name is not found']
        ];
    }
}
