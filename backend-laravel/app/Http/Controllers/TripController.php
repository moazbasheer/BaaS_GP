<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Path;
use App\Models\Trip;
use App\Models\PaymentDetails;
use Auth;
use Stripe;
class TripController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/organization/trips",
     *      operationId="create a trip",
     *      tags={"Trips"},
     *      summary="create a trip",
     *      description="create a trip",
     *      @OA\Parameter(
     *          name="path_id",
     *          description="path id",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="repitition",
     *          description="repitition",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="date",
     *          description="date",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="time",
     *          description="time",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="num_seats",
     *          description="number of seats",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="public",
     *          description="is the trip public 0 or 1",
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
     * Returns status of the update
     */
    public function create_trip(Request $req) {
        $validator = Validator::make($req->all(),[
            'path_id' => 'required|numeric',
            'repitition' => 'required|string', // one-time.
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'num_seats' => 'required|numeric|min:1|max:100',
            'public' => 'required|numeric'
        ]);
        
        if($validator->fails()) {
            $message = [];
            $message = 
            UserController::format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        if($req->num_seats <= 0) {
            return response([
                'status' => false,
                'message' => ['number of seats must be
                 more then zero']
            ], 200);
        }
        $datetime = new \DateTime($req->date . ' ' . $req->time);
        if(now() > $datetime) {
            return response([
                'status' => false,
                'message' => ['this time is not valid']
            ]);
        }
        $path = Path::where('id', $req->path_id)->first();

        $organization = Auth::user()->organization;
        if($req->repitition == "one-time") {
            $trip = Trip::create([
                'path_id' => $path->id,
                'organization_id' => $organization->id,
                'repitition' => $req->repitition,
                'datetime' => $datetime,
                'status' => 0,
                'price' => $path->price,
                'num_seats' => $req->num_seats,
                'public' => $req->public
            ]);
            return response([
                'status' => true,
                'message' => ['trip is added successfully'],
                "trip" => [
                    'id' => $trip->id
                ]
            ], 200);
        } else {

            return response([
                'status' => false,
                'message' => ['repitition is either 
                one-time, daily, weekly']
            ], 200);
        }
    }
    /**
     * @OA\Get(
     *      path="/api/organization/trips",
     *      operationId="get all trips",
     *      tags={"Trips"},
     *      summary="get all trips",
     *      description="get all trips",
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
     * Returns status of the update
     */
    public function get_all_trips(Request $req) {
        $organization = Auth::user()->organization;
        $all_trips = Trip::where('organization_id', $organization->id)
        ->where('datetime', '>', now())->get();
        $trips = [];
        foreach($all_trips as $trip) {
            $trip1 = [];
            $trip1["id"] = $trip->id;
            $trip1["path_id"] = $trip->path->id;
            $trip1["repitition"] = $trip->repitition;
            $trip1["datetime"] = $trip->datetime;
            $trip1["status"] = $trip->status;
            $trip1["price"] = $trip->price;
            $trip1["num_seats"] = $trip->num_seats;
            $trip1["public"] = $trip->public;
            $trips[] = $trip1;
        }
        uasort($trips, function($a, $b) {
            return $a["price"] > $b["price"];
        });
        $trips = array_values($trips);
        return response([
            'status' => true,
            'message' => $trips
        ]);
    }
    /**
     * @OA\Post(
     *      path="/api/organization/trips/pay/{id}",
     *      operationId="pay a trip",
     *      tags={"Trips"},
     *      summary="pay a trip",
     *      description="pay a trip",
     *      @OA\Parameter(
     *          name="payment_method",
     *          description="payment method (wallet or credit)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="card_number",
     *          description="card number(for credit payment method)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="exp_month",
     *          description="expiration month (for credit payment method)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="exp_year",
     *          description="expiration year (for credit payment method)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="CVC",
     *          description="CVC (three-digit number) (for credit payment method)",
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
     */
    public function pay_trip(Request $req) {
        $trip = Trip::find($req->id);
        if($trip == null) {
            return [
                'status' => false,
                'message' => ['trip is not found']
            ];
        }
        if($trip->datetime < now()) {
            return response([
                'status' => false,
                'message' => ['this trip is finished']
            ]);
        }
        if($trip->organization_id != 
        Auth::user()->organization->id
        || is_null($trip)) {
            return [
                'status' => false,
                'message' => ['id is not valid']
            ];
        }
        if($trip->status == 1) {
            return response([
                'status' => false,
                'message' => ['trip is already paid']
            ], 200);
        }
        if($req->payment_method && 
        $req->payment_method == "credit") {
            $validator = Validator::make($req->all(), [
                'card_number' => 'required|size:16',
                'exp_month' => 'required|size:2',
                'exp_year' => 'required|size:4',
                'CVC' => 'required|size:3'
            ]);
            if($validator->fails()) {
                $message = [];
                $message = 
                UserController::format_message($message, $validator);
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
            try{
                $status = Stripe\Charge::create([
                    "amount" => $trip->price * 100,
                    "currency" => "egp",
                    "card" => $token,
                    "description" => "Trip " . $trip->id . " payment" 
                ]);
            } catch(\Exception $e) {
                return response([
                    'status' => false,
                    'message' => [$e->getError()->message]
                ], 200);
            }
            $trip->status = 1;
            $trip->save();
            PaymentDetails::create([
                'trip_id' => $trip->id,
                'amount' => $trip->price,
                'currency' => "egp"
            ]);
            return response([
                'status' => true,
                'message' => ['trip is paid succesfully']
            ], 200);
        } elseif(!$req->payment_method) {
            return response([
                'status' => false,
                'message' => ['The payment method is required']
            ], 200);
        } elseif($req->payment_method == "wallet") {
            $wallet = Auth::user()->organization->wallet;
            if($trip->price > $wallet->balance) {
                return [
                    'status' => false,
                    'message' => ['balance of the wallet isn\'t enough']
                ];
            } else {
                $wallet->balance = $wallet->balance - $trip->price;
                $wallet->save();
                $trip->status = 1;
                $trip->save();
                PaymentDetails::create([
                    'trip_id' => $trip->id,
                    'amount' => $trip->price,
                    'currency' => "egp"
                ]);
                return response([
                    'status' => true,
                    'message' => ['trip is paid succesfully, your balance now is ' . $wallet->balance]
                ], 200);
            }
        } else {

            return response([
                'status' => false,
                'message' => ['The payment method is either wallet or credit']
            ], 200);
        }
    }
    /**
     * @OA\Get(
     *      path="/api/organization/trips/users/{id}",
     *      operationId="get users in a trip",
     *      tags={"Trips"},
     *      summary="get users in a trip",
     *      description="get users in a trip",
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
     * Returns status of the update
     */
    public function get_users_in_trip(Request $req) {
        $trip_id = $req->id;
        $trip = Trip::find($trip_id);
        if($trip == null) {
            return [
                'status' => false,
                'message' => ['trip is not found']
            ];
        }
        if($trip->datetime < now()) {
            return response([
                'status' => false,
                'message' => ['this trip is finished']
            ]);
        }
        $all_clients = $trip->clients;
        $clients = [];
        foreach($all_clients as $client) {
            $user = $client->user;
            $clients[] = [
                'email' => $user->email,
                'role_name' => $user->role_name,
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'username' => $client->username,
                'phone_number' => $client->phone_number
            ];
        }
        $all_passengers = $trip->passengers;
        $passengers = [];
        
        foreach($all_passengers as $passenger) {
            $user = $passenger->user;
            $passengers[] = [
                'email' => $user->email,
                'role_name' => $user->role_name,
                'name' => $passenger->name,
                'phone' => $passenger->phone,
                'address' => $passenger->address,
                'activated' => $passenger->activated
            ];
        }
        return [
            'status' => true,
            'message' => [
                'clients' => $clients,
                'passengers' => $passengers
            ]
        ];
    }

    /**
     * @OA\Get(
     *      path="/api/organization/trips/{id}",
     *      operationId="get trip by id",
     *      tags={"Trips"},
     *      summary="get trip by id",
     *      description="get trip by id",
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
     */
    public function show($id) {
        $trip = Trip::find($id);
        if($trip == null) {
            return [
                'status' => false,
                'message' => ['trip is not found']
            ];
        }
        if($trip->datetime < now()) {
            return response([
                'status' => false,
                'message' => ['this trip is finished']
            ]);
        }
        $trip = [
            'path_id' => $trip->path_id,
            'repitition' => $trip->repitition,
            'status' => $trip->status,
            'price' => $trip->price,
            'num_seats' => $trip->num_seats,
            'public' => $trip->public,
            'datetime' => $trip->datetime
        ];
        return [
            'status' => true,
            'message' => $trip
        ];
    }

    /**
     * @OA\Delete(
     *      path="/api/organization/trips/{id}",
     *      operationId="delete trip by id",
     *      tags={"Trips"},
     *      summary="delete trip by id",
     *      description="delete trip by id",
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
     */
    public function destroy($id) {

        $trip = Trip::find($id);
        
        
        if($trip == null) {
            return [
                'status' => false,
                'message' => ['trip is not found']
            ];
        }
        if($trip->datetime > now()) {
            $wallet = Auth::user()->organization->wallet;
            $wallet->balance += $trip->price;
            $wallet->save();
            $all_clients = $trip->clients;
            foreach($all_clients as $client) {
                $wallet = $client->wallet;
                $wallet->balance += ceil($trip->price / $trip->num_seats + 2);
                $wallet->save();
            }
            $all_passengers = $trip->passengers;
            
            foreach($all_passengers as $passenger) {
                $wallet = $passenger->wallet;
                $wallet->balance += ceil($trip->price / $trip->num_seats + 2);
                $wallet->save();
            }
        }
        $trip->delete();
        return [
            'status' => true,
            'message' => ['trip is deleted successfully']
        ];
    }
}
