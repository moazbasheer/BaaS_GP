<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use Stripe;
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

    public function get_all_trips() {

    }

    public function join_trip() {
        
    }
}
