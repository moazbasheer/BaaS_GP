<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PassengersImport;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Hash;
use Auth;
use App\Models\User;
use App\Models\Passenger;
use Spatie\Permission\Models\Role;

class OrganizationController extends Controller
{
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
     * @OA\Get(
     *      path="/api/organization/passengers",
     *      operationId="get all passengers",
     *      tags={"Passengers"},
     *      summary="get all passengers",
     *      description="get all passengers",
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
     * Returns passengers data.
     */
    public function show_all_passengers() {
        $passengers = Passenger::where('organization_id', Auth::user()->organization->id)->with('user')->get();
        $messages = [];
        foreach($passengers as $passenger) {
            $message = [
                'id' => $passenger->id,
                'email' => $passenger->user->email,
                'name' => $passenger->name,
                'phone' => $passenger->phone,
                'address' => $passenger->address
            ];
            $messages[] = $message;
        }
        return response([
            'status' => true,
            'message' => $messages
        ]);
    }
    /**
     * @OA\Post(
     *      path="/api/organization/passengers",
     *      operationId="create a passenger",
     *      tags={"Passengers"},
     *      summary="create a passenger",
     *      description="create a passenger",
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
     *          name="name",
     *          description="name",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="phone",
     *          description="phone",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="address",
     *          description="address",
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
     * Returns status of insertion.
     */
    public function create_passenger(Request $req) {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'name' => 'required',
            'phone' => 'required|min:11',
            'address' => 'required'
        ]);
        $row = $req->all();
        $message = [];
        if($validator->fails()) {
            $message = $this->format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $user = User::create([
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'role_name' => 'passenger'
        ]);
        
        Passenger::create([
            'user_id' => User::latest()->first()->id,
            'organization_id' => Auth::user()->organization->id,
            'name' => $row['name'],
            'phone' => $row['phone'],
            'address' => $row['address']
        ]);
        $role = Role::where('name', 'passenger')->first();
        $user->assignRole($role);
        return response([
            'status' => true,
            'message' => ['passenger is added successfully']
        ], 200);
    }
    /**
     * @OA\Post(
     *      path="/api/organization/passengers/import",
     *      operationId="import passengers",
     *      tags={"Passengers"},
     *      summary="import passengers",
     *      description="imprt passengers",
     *      @OA\Parameter(
     *          name="file",
     *          description="file",
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
     * Returns status of insertion.
     */
    public function import_passengers_data(Request $req) {
        $validator = Validator::make($req->all(), [
            'file' => 'required'
        ]);
        $message = [];
        if($validator->fails()) {
            $message = $this->format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $import = new PassengersImport();
        $import->import($req->file('file'));
        //Excel::import(new PassengersImport, $req->file);
        return response([
            'status' => true,
            'message' => ['passengers are added succesfully']
        ]);
    }
}
