<?php

use App\Http\Controllers\Api\SorularController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::get('/sorular', [SorularController::class, 'index'])->middleware('auth:sanctum');
Route::get('/sorular/{id}', [SorularController::class, 'show'])->middleware('auth:sanctum');;
Route::post('/sorular', [SorularController::class, 'store'])->middleware('auth:sanctum');;
Route::put('/sorular/{id}', [SorularController::class, 'update'])->middleware('auth:sanctum');;
Route::delete('/sorular/{id}', [SorularController::class, 'destroy'])->middleware('auth:sanctum');;

Route::get('/scores', [\App\Http\Controllers\Api\ScoreController::class, 'index']);
Route::post('/scores', [\App\Http\Controllers\Api\ScoreController::class, 'store']);
