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
            'path_name' => 'required|string',
            'repitition' => 'required|string', // one-time.
            'date' => 'required|date',
            'time' => 'required|date_format:H:i'
        ]);
        $path = Path::where('name', $req->path_name)->first();

        if($validator->fails()) {
            $message = [];
            $message = UserController::format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $organization = Auth::user()->organization;
        if($req->repitition == "one-time") {
            Trip::create([
                'path_id' => $path->id,
                'organization_id' => $organization->id,
                'repitition' => $req->repitition,
                'date' => $req->date,
                'time' => $req->time,
                'status' => 0,
                'price' => $path->price
            ]);
            return response([
                'status' => true,
                'message' => ['trip is added successfully']
            ], 200);
        } else {

            return response([
                'status' => false,
                'message' => ['repition is either one-time, daily, weekly']
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
        $all_trips = Trip::get();
        $trips = [];
        foreach($all_trips as $trip) {
            $trip1 = [];
            $trip1["id"] = $trip->id;
            $trip1["path_name"] = $trip->path->name;

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
            $trip1["distance"] = $trip->distance;
            $trip1["time"] = $trip->time;
            $trip1["price"] = $trip->price;
            $trips[] = $trip1;
        }
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
    public function pay_trip(Request $req) {
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
        $trip = Trip::find($req->id);
        if($trip->status == 1) {
            return response([
                'status' => false,
                'message' => ['trip is already paid']
            ], 200);
        }
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        
        $name = Auth::user()->organization->name;
        $result = Stripe\Token::create([
            "card" => [
                "name" => $req->name,
                "number" => $req->card_number,
                "exp_month" => $req->exp_month,
                "exp_year" => $req->exp_year,
                "cvc" => $req->CVC
            ]
        ]);
        $token = $result['id'];
        Stripe\Charge::create([
            "amount" => $trip->price * 100,
            "currency" => "egp",
            "card" => $token,
            "description" => "Trip " . $trip->id . " payment" 
        ]);
        $trip->status = 1;
        $trip->save();
        PaymentDetails::create([
            'trip_id' => $trip->id,
            'amount' => $trip->price,
            'currency' => "egp"
        ]);
        // assign driver.

        return response([
            'status' => true,
            'message' => ['trip is paid succesfully']
        ], 200);
    }
}
