<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthApiController;

Route::get('ping', fn () => response()->json(['pong' => true])); // debug
Route::post('login', [AuthApiController::class, 'login']);       // -> /api/login
