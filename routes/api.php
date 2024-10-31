<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\api\ContribuyenteController;
use App\Http\Controllers\api\NotificacionController;
use App\Http\Controllers\api\PeticionController;
use App\Http\Controllers\api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);

Route::group([
    "middleware" => ["auth:api"]
], function () {

    Route::get("profile", [ApiController::class, "profile"]);
    Route::get("logout", [ApiController::class, "logout"]);
    Route::resource('contribuyente', ContribuyenteController::class);
    Route::resource('peticiones', PeticionController::class);
    Route::resource('User', UserController::class);
    Route::resource('a', UserController::class);
    Route::resource('notificaciones', NotificacionController::class);
});
