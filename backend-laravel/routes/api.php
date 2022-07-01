<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\PathController;
use App\Http\Controllers\OrganizationController;

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

        Route::get('/paths/{route_id}', [PathController::class, 'index']);
        Route::apiResource('paths', PathController::class)->only(['store']);
        Route::delete('paths/{path_name}', [PathController::class, 'destroy']);
        Route::put('paths/{path_name}', [PathController::class, 'update']);
        
    });
    Route::post('/logout', [UserController::class, 'logout']);
});
