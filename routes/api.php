<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

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
Route::get('users-list', [UserController::class, 'index']);
Route::get('get-user', [UserController::class, 'show']);
Route::post('store-user', [UserController::class, 'store']);
Route::post('update-user', [UserController::class, 'update']);
Route::post('destroy-user', [UserController::class, 'destroy']);
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
