<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Wallet;
use App\Models\Trip;
use App\Models\Booking;
use Validator;
use Stripe;

class PassengerController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/passenger/wallet",
     *      operationId="check the balance for passenger",
     *      tags={"Passengers"},
     *      summary="check the balance for passenger",
     *      description="check the balance for passenger",
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
    public function check_balance() {
        $wallet = Auth::user()->passenger->wallet;
        return response([
            'status' => true,
            'message' => [
                'balance' => $wallet->balance
            ]
        ]);
    }
    /**
     * @OA\Post(
     *      path="/api/passenger/wallet/charge",
     *      operationId="charge the balance for passengers",
     *      tags={"Passengers"},
     *      summary="charge the balance for passengers",
     *      description="charge the balance for passengers",
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
        $passenger = Auth::user()->passenger;
        try{
            $status = Stripe\Charge::create([
                "amount" => $req->amount * 100,
                "currency" => "egp",
                "card" => $token,
                "description" => "Charging wallet of passenger" . $passenger->id
            ]);
        } catch(\Exception $e) {
            return response([
                'status' => false,
                'message' => [$e->getError()->message]
            ], 200);
        }
        
        $wallet = $passenger->wallet;
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
     * @OA\Post(
     *      path="/api/passenger/trips/inside",
     *      operationId="get the trips inside the organization",
     *      tags={"Passengers"},
     *      summary="get the trips inside the organization",
     *      description="get the trips inside the organization",
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
    public function get_trips_inside_organization() {
        $passenger = Auth::user()->passenger;
        $organization = $passenger->organization;
        $all_trips = Trip::where('organization_id', $organization->id) 
            ->where('status', 1)->where('datetime', '>', now())->get();
        $trips = [];
        foreach($all_trips as $trip) {
            if($trip->num_user == $trip->num_seats) {
                $bookings = Booking::where([
                    'passenger_id' => $passenger->id,
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
            $trip1["price"] = ceil($trip->price / $trip->num_seats);
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
            $bookings = Booking::where(['passenger_id' => $passenger->id, 'trip_id' => $trip->id])->get();
            if(count($bookings) != 0) {
                $trip1["joined"] = true;
            } else {
                $trip1["joined"] = false;
            }
            $trip1["num_seats"] = $trip->num_seats;
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
     *      path="/api/passenger/trips/outside",
     *      operationId="get the trips outside the organization",
     *      tags={"Passengers"},
     *      summary="get the trips outside the organization",
     *      description="get the trips outside the organization",
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
    public function get_trips_outside_organization() {
        $passenger = Auth::user()->passenger;
        $organization = $passenger->organization;
        $all_trips = Trip::where('organization_id', '!=', $organization->id) 
            ->where('status', 1)->where('public', 1)
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
            $trip1["price"] = ceil($trip->price / $trip->num_seats);
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
            $bookings = Booking::where(['passenger_id' => $passenger->id, 'trip_id' => $trip->id])->get();
            if(count($bookings) != 0) {
                $trip1["joined"] = true;
            } else {
                $trip1["joined"] = false;
            }
            $trip1["num_seats"] = $trip->num_seats;
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
     *      path="/api/passenger/trips/{id}",
     *      operationId="join a trip for passengers",
     *      tags={"Passengers"},
     *      summary="join a trip for passengers",
     *      description="join a trip for passengers",
     *      @OA\Parameter(
     *          name="payment_method",
     *          description="payment method",
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
     *          name="CVC (for credit)",
     *          description="CVC",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="distance",
     *          description="distance",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="time",
     *          description="time",
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
        
        $organization = Auth::user()->passenger->organization;
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
                'message' => ['trip is finished']
            ]);
        }
        $trip_id = $req->id;
        $trips = Trip::where('id', $trip_id)->get()->toArray();
        if(count($trips) == 0) {
            return response([
                'status' => false,
                'message' => ['id is not valid']
            ], 200);
        }
        $passenger = Auth::user()->passenger;
        $bookings = Booking::where(['passenger_id' => $passenger->id, 'trip_id' => $trip_id])->get()->toArray();
        if(count($bookings) != 0) {
            return response([
                'status' => false,
                'message' => ['this passenger has already joined this trip']
            ], 200);
        }
        $trip_organization = $trip->organization;
        if($organization->id != $trip_organization->id) {
            
            $trip = Trip::where('id', $trip_id)->first();
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
                
                $name = Auth::user()->passenger->name;
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
                        "amount" => ceil((0.02 * $req->distance + $req->time / 3000 + 15) / $trip->num_seats)* 100,
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
                $wallet = Auth::user()->passenger->wallet;
                if(ceil((0.02 * $req->distance + $req->time / 3000 + 15) / $trip->num_seats) > $wallet->balance) {
                    return [
                        'status' => false,
                        'message' => ['balance of the wallet isn\'t enough']
                    ];
                } else {
                    $wallet->balance -= ceil((0.02 * $req->distance + $req->time / 3000 + 15) / $trip->num_seats);
                    $wallet->save();
                }
            } else {

                return response([
                    'status' => false,
                    'message' => ['The payment method is either wallet or credit']
                ], 200);
            }
        }
        
        $trip_id = $req->id;
        
        $trip = Trip::where('id', $trip_id)->first();
        if($trip->num_users < $trip->num_seats) {
            $passenger->trips()->attach($trip_id);
            $trip->num_users += 1;
            $trip->save();
            $booking = Booking::where(['passenger_id' => $passenger->id, 'trip_id' => $trip_id])->first();
            $booking->distance = $req->distance;
            $booking->time = $req->time;
            $booking->price = ceil((0.02 * $req->distance + $req->time / 3000 + 15) / $trip->num_seats);
            $booking->save();
            return response([
                'status' => true,
                'message' => ['the passenger joined the trip successfully']
            ], 200);
        } else {
            
            return response([
                'status' => false,
                'message' => ['the number of seats in the trip is not enough']
            ], 200);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/passenger/trips/joined",
     *      operationId="get joined trips for passenger",
     *      tags={"Passengers"},
     *      summary="get joined trips for passenger",
     *      description="get joined trips for passenger",
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
        $passenger = Auth::user()->passenger;
        $all_trips = Auth::user()->passenger->trips;
        $trips = [];
        foreach($all_trips as $trip) {

            if($trip->datetime < now()) {
                continue;
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
            $trip1["date"] = $trip->date;
            $trip1["time"] = $trip->time;
            $trip1["status"] = $trip->status;
            $trip1["path_distance"] = $trip->path->distance;
            $trip1["path_time"] = $trip->path->time;
            $passenger = Auth::user()->passenger;
            $booking = Booking::where(['passenger_id' => $passenger->id, 'trip_id' => $trip->id])->first();
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
            $bookings = Booking::where(['passenger_id' => $passenger->id, 'trip_id' => $trip->id])->get();
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
     *      path="/api/passenger/trips/cancel/{id}",
     *      operationId="cancel a trip for passengers",
     *      tags={"Passengers"},
     *      summary="cancel a trip for passengers",
     *      description="cancel a trip for passengers",
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
        $organization = Auth::user()->passenger->organization;
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
                'message' => ['trip is finished']
            ]);
        }
        $trip_organization = $trip->organization;
        $trip_id = $req->id;
        $trips = Trip::where('id', $trip_id)->get()->toArray();
        if(count($trips) == 0) {
            return response([
                'status' => false,
                'message' => ['id is not valid']
            ], 200);
        }
        $passenger = Auth::user()->passenger;
        $bookings = Booking::where(['passenger_id' => $passenger->id, 'trip_id' => $trip_id])->get()->toArray();
        if(count($bookings) == 0) {
            return response([
                'status' => false,
                'message' => ['this passenger hasn\'t joined this trip']
            ], 200);
        }
        if($organization->id != $trip_organization->id) {
            
            
            $trip = Trip::where('id', $trip_id)->first();
            $wallet = $passenger->wallet;
            $booking = Booking::where(['passenger_id' => $passenger->id, 'trip_id' => $trip_id])->first();
            $wallet->balance += $booking->price;
            $wallet->save();
            
        }
        $passenger = Auth::user()->passenger;
        $passenger->trips()->detach($req->id);
        $passenger->save();
        $trip->num_users -= 1;
        $trip->save();
        return response([
            'status' => true,
            'message' => ['the passenger cancelled the trip successfully']
        ], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/passenger/complaints",
     *      operationId="make a complaint for passenger",
     *      tags={"Clients"},
     *      summary="make a complaint for passenger",
     *      description="make a complaint for passenger",
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
        if($trip->datetime < now()) {
            return response([
                'status' => false,
                'message' => ['trip is finished']
            ]);
        }
        $passenger_id = Auth::user()->passenger->id;
        $bookings = Booking::where(['trip_id' => $req->trip_id, 
        'passenger_id' => $passenger_id])->get();
        if(count($bookings) == 0) {
            return response([
                'status' => false,
                'message' => ['the passenger doesn\'t join the trip']
            ]);
        }
        $complaints = Complaint::where(['trip_id' => $req->trip_id, 
        'passenger_id' => $passenger_id])->get();
        if(count($complaints) > 0) {
            return response([
                'status' => false,
                'message' => ['you already have a complaint on this trip']
            ]);
        }
        
        $complaint = Complaint::create([
            'passenger_id' => $passenger_id,
            'trip_id' => $req->trip_id,
            'message' => $req->message
        ]);
        return response([
            'status' => true,
            'message' => ['complaint is added successfully']
        ]);
    }
}
