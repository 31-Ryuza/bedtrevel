<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelControllerNoResource;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingHotelController;
use App\Http\Controllers\SuperadminController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//API route for register new user
Route::post('/register', [AuthController::class, 'register']);
//API route for login user
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    // List Hotel
    Route::get('/hotel', [HotelControllerNoResource::class, 'getAll']);
    Route::get('/hotel/{id}', [HotelControllerNoResource::class, 'getById']);
    Route::delete('/hotel/{id}', [HotelControllerNoResource::class, 'deleteById']);
    Route::put('/hotel/{id}', [HotelControllerNoResource::class, 'updateById']);
    Route::post('/hotel', [HotelControllerNoResource::class, 'postHotel']);

    // List Booking
    Route::get('/booking', [BookingHotelController::class, 'getAll']);
    Route::post('/booking', [BookingHotelController::class, 'postBooking']);
    Route::put('/booking/{id}', [BookingHotelController::class, 'updateById']);
    Route::delete('/booking/{id}', [BookingHotelController::class, 'deleteById']);
    Route::get('/booking/{id}', [BookingHotelController::class, 'getById']);
    Route::put('/update-status-booking', [BookingHotelController::class, 'updateStatus']);

    Route::get('/superadmin', [SuperadminController::class, 'getAll']);
    Route::post('/superadmin', [SuperadminController::class, 'userCreate']);
    Route::delete('/superadmin/{id}', [SuperadminController::class, 'deleteById']);
    Route::get('/superadmin/{id}', [SuperadminController::class, 'getById']);
    Route::put('/update-role', [AuthController::class, 'updateRole']);

    // API route for logout user
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile-user', function(Request $request) {
        return auth()->user();
    });
});

