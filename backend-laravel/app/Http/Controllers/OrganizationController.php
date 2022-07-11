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
use App\Models\Wallet;
use Stripe;
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
                'address' => $passenger->address,
                'activated' => $passenger->activated
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
        
        $passenger = Passenger::create([
            'user_id' => User::latest()->first()->id,
            'organization_id' => Auth::user()->organization->id,
            'name' => $row['name'],
            'phone' => $row['phone'],
            'address' => $row['address']
        ]);
        $role = Role::where('name', 'passenger')->first();
        $user->assignRole($role);
        Wallet::create([
            'passenger_id' => $passenger->id,
            'balance' => 0
        ]);
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
     *      description="import passengers",
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

    /**
     * @OA\Put(
     *      path="/api/organization/passengers/deactivate/{id}",
     *      operationId="deactivate a passenger",
     *      tags={"Passengers"},
     *      summary="deactivate a passenger",
     *      description="deactivate a passenger",
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
     * Returns all passengers data.
     */
    public function deactivate_passenger(Request $req) {
        
        $passenger = Passenger::where('id', $req->id)->first();
        if($passenger->activated == 0) {
            return response([
                'status' => false,
                'message' => ['passenger is already unactivated']
            ], 200);
        }
        $passenger->activated = 0;
        $passenger->save();
        return response([
            'status' => true,
            'message' => ['passenger is deactivated successfully']
        ], 200);
    }

    /**
     * @OA\Put(
     *      path="/api/organization/passengers/activate/{id}",
     *      operationId="activate a passenger",
     *      tags={"Passengers"},
     *      summary="activate a passenger",
     *      description="activate a passenger",
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
     * Returns all passengers data.
     */
    public function activate_passenger(Request $req) {
        
        $passenger = Passenger::where('id', $req->id)->first();
        if($passenger->activated == 1) {
            return response([
                'status' => false,
                'message' => ['passenger is already activated']
            ], 200);
        }
        $passenger->activated = 1;
        $passenger->save();
        return response([
            'status' => true,
            'message' => ['passenger is activated successfully']
        ], 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/organization/passengers/{id}",
     *      operationId="delete a passenger",
     *      tags={"Passengers"},
     *      summary="delete a passenger",
     *      description="delete a passenger",
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
     * Returns all passengers data.
     */
    public function remove_passenger(Request $req) {
        
        $passenger = Passenger::where('id', $req->id)->first();
        if($passenger == null) {
            return [
                'status' => false,
                'message' => ['id is not valid']
            ];
        }
        $user = $passenger->user;
        $passenger->delete();
        $user->delete();
        
        return response([
            'status' => true,
            'message' => ['passenger is deleted successfully']
        ], 200);
    }
    /**
     * @OA\Get(
     *      path="/api/organization/wallet",
     *      operationId="check the balance",
     *      tags={"Organizations"},
     *      summary="check the balance",
     *      description="check the balance",
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
     * Returns all passengers data.
     */
    public function check_balance() {
        $wallet = Auth::user()->organization->wallet;
        return response([
            'status' => true,
            'message' => [
                'balance' => $wallet->balance
            ]
        ]);
    }
    /**
     * @OA\Post(
     *      path="/api/organization/wallet/charge",
     *      operationId="charge the balance",
     *      tags={"Organizations"},
     *      summary="charge the balance",
     *      description="charge the balance",
     *      @OA\Parameter(
     *          name="card_number",
     *          description="card number",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="exp_month",
     *          description="expiration month",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="exp_year",
     *          description="expiration year",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="CVC",
     *          description="CVC",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="amount",
     *          description="amount",
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
     * Returns all passengers data.
     */
    public function charge_balance(Request $req) {
        $validator = Validator::make($req->all(), [
            'card_number' => 'required|size:16',
            'exp_month' => 'required|size:2',
            'exp_year' => 'required|size:4',
            'CVC' => 'required|size:3',
            'amount' => 'required|numeric'
        ]);
        if($req->amount < 1) {
            return [
                'status' => false,
                'message' => ['amount must be a positive integer']
            ];
        }
        if($validator->fails()) {
            $message = [];
            $message = UserController::format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            
        $name = Auth::user()->organization->name;
        try {
            $result = Stripe\Token::create([
                "card" => [
                    "name" => $req->name,
                    "number" => $req->card_number,
                    "exp_month" => $req->exp_month,
                    "exp_year" => $req->exp_year,
                    "cvc" => $req->CVC
                ]
            ]);
        } catch(\Exception $e) {
            return response([
                'status' => false,
                'message' => [$e->getError()->message]
            ], 200);
        }
        $token = $result['id'];
        $organization = Auth::user()->organization;
        try{
            $status = Stripe\Charge::create([
                "amount" => $req->amount * 100,
                "currency" => "egp",
                "card" => $token,
                "description" => "Charging wallet of organization" . $organization->id
            ]);
        } catch(\Exception $e) {
            return response([
                'status' => false,
                'message' => [$e->getError()->message]
            ], 200);
        }
        
        $wallet = $organization->wallet;
        $wallet->balance += $req->amount;
        $wallet->save();
        return response([
            'status' => true,
            'message' => [
                'wallet is charged successfully your balance now is ' . $wallet->balance
            ]
        ]);
    }
    
}
