<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use Stripe;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Complaint;
class ClientController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/client/wallet",
     *      operationId="check the balance for client",
     *      tags={"Clients"},
     *      summary="check the balance for client",
     *      description="check the balance for client",
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
        $wallet = Auth::user()->client->wallet;
        return response([
            'status' => true,
            'message' => [
                'balance' => $wallet->balance
            ]
        ]);
    }
    /**
     * @OA\Post(
     *      path="/api/client/wallet/charge",
     *      operationId="charge the balance for client",
     *      tags={"Clients"},
     *      summary="charge the balance for client",
     *      description="charge the balance for client",
     *      @OA\Parameter(
     *          name="card_number",
     *          description="card number (for credit)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="exp_month",
     *          description="expiration month (for credit)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="exp_year",
     *          description="expiration year (for credit)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="CVC",
     *          description="CVC (for credit)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="amount",
     *          description="amount (for credit)",
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
            'amount' => 'required'
        ]);
        if(gettype($req->amount) != "integer" || $req->amount < 1) {
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
        $client = Auth::user()->client;
        try{
            $status = Stripe\Charge::create([
                "amount" => $req->amount * 100,
                "currency" => "egp",
                "card" => $token,
                "description" => "Charging wallet of client" . $client->id
            ]);
        } catch(\Exception $e) {
            return response([
                'status' => false,
                'message' => [$e->getError()->message]
            ], 200);
        }
        
        $wallet = $client->wallet;
        $wallet->balance += $req->amount;
        $wallet->save();
        return response([
            'status' => true,
            'message' => [
                'wallet is charged successfully your balance now is ' . $wallet->balance
            ]
        ]);
    }
    /**
     * @OA\Get(
     *      path="/api/client/trips",
     *      operationId="get all trips for client",
     *      tags={"Clients"},
     *      summary="get all trips for client",
     *      description="get all trips for client",
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
     */
    public function get_all_trips() {
        $client = Auth::user()->client;
        $all_trips = Trip::where('status', 1)->where('public', 1)
        ->where('datetime', '>', now())->get();
        $trips = [];
        foreach($all_trips as $trip) {
            if($trip->num_user == $trip->num_seats) {
                $bookings = Booking::where([
                    'client_id' => $client->id,
                    'trip_id' => $trip->id
                ])->get();
                if(count($bookings) == 0) {
                    continue;
                }
            }
            $complaints = Complaint::where([
                'client_id' => $client->id,
                'trip_id' => $trip->id
            ])->get();
            if(count($complaints) > 0) {
                continue;
            }
            $trip1 = [];
            $trip1["id"] = $trip->id;
            $trip1["path_name"] = $trip->path->name;
            $trip1["route"] = [
                'source' => $trip->path->route->source,
                'destination' => $trip->path->route->destination
            ];
            $all_stops = $trip->path->stops->toArray();
            $trip1["stops"] = [];
            foreach($all_stops as $stop) {
                $stop1 = [
                    'name' => $stop["name"],
                    'longitude' => $stop["longitude"],
                    'latitude' => $stop["latitude"]
                ];
                $trip1["stops"][] = $stop1;
            }
            $trip1["repitition"] = $trip->repitition;
            $trip1["datetime"] = $trip->datetime;
            $trip1["status"] = $trip->status;
            $trip1["path_distance"] = $trip->path->distance;
            $trip1["path_time"] = $trip->path->time;
            $trip1["price"] = $trip->price / $trip->num_seats;
            $trip1["num_seats"] = $trip->num_seats;
            $trip1["public"] = $trip->public;
            $organization = $trip->organization;
            $user = $organization->user;
            $trip1["organization"] = [
                'email' => $user->email,
                'name' => $organization->name,
                'phone_number' => $organization->phone_number,
                'postal_code' => $organization->postal_code,
                'address' => $organization->address
            ];
            $bookings = Booking::where(['client_id' => $client->id, 'trip_id' => $trip->id])->get();
            if(count($bookings) != 0) {
                $trip1["joined"] = true;
            } else {
                $trip1["joined"] = false;
            }
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
     * @OA\Get(
     *      path="/api/client/trips/joined",
     *      operationId="get joined trips for client",
     *      tags={"Clients"},
     *      summary="get joined trips for client",
     *      description="get joined trips for client",
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
     */
    public function get_joined_trips() {
        $client = Auth::user()->client;
        $all_trips = Auth::user()->client->trips;
        $trips = [];
        foreach($all_trips as $trip) {
            if($trip->datetime < now()) {
                return response([
                    'status' => false,
                    'message' => ['trip is finished']
                ]);
            }
            $complaints = Complaint::where([
                'client_id' => $client->id,
                'trip_id' => $trip->id
            ])->get();
            if(count($complaints) > 0) {
                continue;
            }
            $trip1 = [];
            $trip1["id"] = $trip->id;
            $trip1["path_name"] = $trip->path->name;
            $trip1["route"] = [
                'source' => $trip->path->route->source,
                'destination' => $trip->path->route->destination
            ];
            $all_stops = $trip->path->stops->toArray();
            $trip1["stops"] = [];
            foreach($all_stops as $stop) {
                $stop1 = [
                    'name' => $stop["name"],
                    'longitude' => $stop["longitude"],
                    'latitude' => $stop["latitude"]
                ];
                $trip1["stops"][] = $stop1;
            }
            $trip1["repitition"] = $trip->repitition;
            $trip1["datetime"] = $trip->datetime;
            $trip1["status"] = $trip->status;
            $trip1["path_distance"] = $trip->path->distance;
            $trip1["path_time"] = $trip->path->time;
            $client = Auth::user()->client;
            $booking = Booking::where(['client_id' => $client->id, 'trip_id' => $trip->id])->first();
            $trip1["price"] = $booking->price;
            $trip1["num_seats"] = $trip->num_seats;
            $trip1["public"] = $trip->public;
            $organization = $trip->organization;
            $user = $organization->user;
            $trip1["organization"] = [
                'email' => $user->email,
                'name' => $organization->name,
                'phone_number' => $organization->phone_number,
                'postal_code' => $organization->postal_code,
                'address' => $organization->address
            ];
            $bookings = Booking::where(['client_id' => $client->id, 'trip_id' => $trip->id])->get();
            if(count($bookings) != 0) {
                $trip1["joined"] = true;
            } else {
                $trip1["joined"] = false;
            }
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
     *      path="/api/client/trips/{id}",
     *      operationId="join a trip",
     *      tags={"Clients"},
     *      summary="join a trip",
     *      description="join a trip",
     *      @OA\Parameter(
     *          name="payment_method",
     *          description="payment method (wallet or credit)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="card_number",
     *          description="card number (for credit)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="exp_month",
     *          description="expiration month (for credit)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="exp_year",
     *          description="expiration year (for credit)",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="CVC",
     *          description="CVC (for credit)",
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
    public function join_trip(Request $req) {
        $validator = Validator::make($req->all(), [
            'distance' => 'required|numeric|min:0',
            'time' => 'required|numeric|min:0',
        ]);
        if($validator->fails()) {
            $message = [];
            $message = UserController::format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $trip_id = $req->id;
        $trip = Trip::where('id', $trip_id)->first();
        if($trip == null) {
            return [
                'status' => false,
                'message' => ['trip is not found']
            ];
        }
        if($trip->datetime < now()) {
            return response([
                'status' => false,
                'message' => ['trip is finished']
            ]);
        }
        if($req->payment_method && $req->payment_method == "credit") {
            $validator = Validator::make($req->all(), [
                'card_number' => 'required|size:16',
                'exp_month' => 'required|size:2',
                'exp_year' => 'required|size:4',
                'CVC' => 'required|size:3'
            ]);
            if($validator->fails()) {
                $message = [];
                $message = UserController::format_message($message, $validator);
                return response([
                    'status' => false,
                    'message' => $message
                ], 200);
            }
            
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            
            $name = Auth::user()->client->name;
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
                    "amount" => ceil((0.01 * $distance + $time / 60000 + 15) / $trip->num_seats) * 100,
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
        } elseif(!$req->payment_method) {
            return response([
                'status' => false,
                'message' => ['The payment method is required']
            ], 200);
        } elseif($req->payment_method == "wallet") {
            $wallet = Auth::user()->client->wallet;
            if(ceil((0.01 * $distance + $time / 60000 + 15) / $trip->num_seats) > $wallet->balance) {
                return [
                    'status' => false,
                    'message' => ['balance of the wallet isn\'t enough']
                ];
            } else {
                $wallet->balance -= ceil((0.01 * $distance + $time / 60000 + 15) / $trip->num_seats);
                $wallet->save();
            }
        } else {

            return response([
                'status' => false,
                'message' => ['The payment method is either wallet or credit']
            ], 200);
        }
        
        $trips = Trip::where('id', $trip_id)->get()->toArray();
        if(count($trips) == 0) {
            return response([
                'status' => false,
                'message' => ['id is not valid']
            ], 200);
        }
        $client = Auth::user()->client;
        $bookings = Booking::where(['client_id' => $client->id, 'trip_id' => $trip_id])->get()->toArray();
        if(count($bookings) != 0) {
            return response([
                'status' => false,
                'message' => ['this client has already joined this trip']
            ], 200);
        }
        $trip = Trip::where('id', $trip_id)->first();
        if($trip->num_users < $trip->num_seats) {
            $client->trips()->attach($trip_id);
            $trip->num_users += 1;
            $trip->save();
            $booking = Booking::where(['client_id' => $client->id, 'trip_id' => $trip_id])->first();
            $booking->distance = $req->distance;
            $booking->time = $req->time;
            $booking->price = ceil((0.01 * $distance + $time / 60000 + 15) / $trip->num_seats);
            $booking->save();
            return response([
                'status' => true,
                'message' => ['the client joined the trip successfully']
            ], 200);
        } else {
            
            return response([
                'status' => false,
                'message' => ['the number of seats in the trip is not enough']
            ], 200);
        }
    }
    /**
     * @OA\Post(
     *      path="/api/client/trips/cancel/{id}",
     *      operationId="cancel a trip",
     *      tags={"Clients"},
     *      summary="cancel a trip",
     *      description="cancel a trip",
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
     */
    public function cancel_trip(Request $req) {
        $trip_id = $req->id;
        $trips = Trip::where('id', $trip_id)->get()->toArray();
        if(count($trips) == 0) {
            return response([
                'status' => false,
                'message' => ['id is not valid']
            ], 200);
        }
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
                'message' => ['trip is finished']
            ]);
        }

        $client = Auth::user()->client;
        $bookings = Booking::where(['client_id' => $client->id, 'trip_id' => $trip_id])->get()->toArray();
        if(count($bookings) == 0) {
            return response([
                'status' => false,
                'message' => ['this client hasn\'t joined this trip']
            ], 200);
        }
        
        $trip = Trip::where('id', $trip_id)->first();
        $wallet = $client->wallet;
        $booking = Booking::where(['client_id' => $client->id, 'trip_id' => $trip_id])->first();
        $wallet->balance += $booking->price;
        $wallet->save();
        $client->trips()->detach($trip_id);
        $client->save();

        $trip->num_users -= 1;
        $trip->save();
        return response([
            'status' => true,
            'message' => ['the client cancelled the trip successfully']
        ], 200);
    }
    /**
     * @OA\Post(
     *      path="/api/client/complaints",
     *      operationId="make a complaint for client",
     *      tags={"Clients"},
     *      summary="make a complaint for client",
     *      description="make a complaint for client",
     *      @OA\Parameter(
     *          name="message",
     *          description="message",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="trip_id",
     *          description="trip id",
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
     */
    public function make_complaint(Request $req) {
        $validator = Validator::make($req->all(), [
            'trip_id' => 'required|numeric',
            'message' => 'required|string'
        ]);
        if($validator->fails()) {
            $message = [];
            $message = UserController::format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $trip = Trip::find($req->trip_id);
        if($trip == null) {
            return [
                'status' => false,
                'message' => ['trip is not found']
            ];
        }
        if($trip->datetime < now()) {
            return response([
                'status' => false,
                'message' => ['trip is finished']
            ]);
        }
        $client_id = Auth::user()->client->id;
        $bookings = Booking::where(['trip_id' => $req->trip_id, 
        'client_id' => $client_id])->get();
        if(count($bookings) == 0) {
            return response([
                'status' => false,
                'message' => ['the client doesn\'t join the trip']
            ]);
        }
        $complaints = Complaint::where(['trip_id' => $req->trip_id, 
        'client_id' => $client_id])->get();
        if(count($complaints) > 0) {
            return response([
                'status' => false,
                'message' => ['you already have a complaint on this trip']
            ]);
        }
        
        $complaint = Complaint::create([
            'client_id' => $client_id,
            'trip_id' => $req->trip_id,
            'message' => $req->message
        ]);
        return response([
            'status' => true,
            'message' => ['complaint is added successfully']
        ]);
    }
}
