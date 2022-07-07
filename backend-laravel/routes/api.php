<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\PathController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PassengerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/login', function() {
    return response([
        'status' => false,
        'message' => "Unautenticated"
    ], 200);
})->name('login');

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('/user', [UserController::class, 'user_info']);
    Route::group(['prefix' => 'organization', 'middleware' => 'role:organization'], function() {
        Route::apiResource('routes', RouteController::class);

        Route::post('/passengers/import', [OrganizationController::class, 'import_passengers_data']);
        Route::post('/passengers', [OrganizationController::class, 'create_passenger']);
        Route::get('/passengers', [OrganizationController::class, 'show_all_passengers']);
        Route::put('/passengers/deactivate/{id}', [OrganizationController::class, 'deactivate_passenger']);
        Route::put('/passengers/activate/{id}', [OrganizationController::class, 'activate_passenger']);
        Route::delete('/passengers/{id}', [OrganizationController::class, 'remove_passenger']);

        Route::get('/paths/{route_id}', [PathController::class, 'index']);
        Route::get('/paths/id/{id}', [PathController::class, 'show']);
        Route::apiResource('paths', PathController::class)->only(['store']);
        Route::delete('paths/{path_id}', [PathController::class, 'destroy']);
        Route::put('paths/{path_id}', [PathController::class, 'update']);
        Route::get('/paths', [PathController::class, 'get_all_paths']);
        
        Route::post('/trips', [TripController::class, 'create_trip']);
        Route::get('/trips', [TripController::class, 'get_all_trips']);
        Route::post('/trips/pay/{id}', [TripController::class, 'pay_trip']);
        Route::get('/trips/users/{id}', [TripController::class, 'get_users_in_trip']);
        
        Route::get('wallet', [OrganizationController::class, 'check_balance']);
        Route::post('wallet/charge', [OrganizationController::class, 'charge_balance']);

    });
    Route::group(['prefix' => 'client', 'middleware' => 'role:client'], function() {
        
        Route::get('wallet', [ClientController::class, 'check_balance']);
        Route::post('wallet/charge', [ClientController::class, 'charge_balance']);

        Route::get('/trips', [ClientController::class, 'get_all_trips']);
        Route::get('/trips/joined', [ClientController::class, 'get_joined_trips']);
        Route::post('/trips/{id}', [ClientController::class, 'join_trip']);
        Route::post('/trips/cancel/{id}', [ClientController::class, 'cancel_trip']);

        Route::post('/complaints', [ClientController::class, 'make_complaint']);
    });
    Route::group(['prefix' => 'passenger', 'middleware' => 'role:passenger'], function() {
        
        Route::get('wallet', [PassengerController::class, 'check_balance']);
        Route::post('wallet/charge', [PassengerController::class, 'charge_balance']);

        Route::get('trips/inside', [PassengerController::class, 'get_trips_inside_organization']);
        Route::get('trips/outside', [PassengerController::class, 'get_trips_outside_organization']);
        Route::post('trips/{id}', [PassengerController::class, 'join_trip']);
        Route::get('/trips/joined', [PassengerController::class, 'get_joined_trips']);
        Route::post('/trips/cancel/{id}', [PassengerController::class, 'cancel_trip']);
        Route::post('/complaints', [PassengerController::class, 'make_complaint']);
    });
    Route::group(['prefix' => 'admin', 'middleware' => 'role:admin'], function() {
        Route::post('/drivers', [DriverController::class, 'create_driver']);
        Route::post('/drivers/import', [DriverController::class, 'import_drivers_data']);
    });
    Route::post('/logout', [UserController::class, 'logout']);
});
