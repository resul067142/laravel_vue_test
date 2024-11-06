<?php

use App\Http\Controllers\Api\SorularController;
use App\Http\Controllers\Api\ScoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController; // FileController'ı dahil ettik

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// AuthController üzerinden login işlemi
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// Sorular CRUD işlemleri
Route::get('/sorular', [SorularController::class, 'index']);
Route::get('/sorular/{id}', [SorularController::class, 'show'])->middleware('auth:sanctum');
Route::post('/sorular', [SorularController::class, 'store'])->middleware('auth:sanctum');
Route::put('/sorular/{id}', [SorularController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/sorular/{id}', [SorularController::class, 'destroy'])->middleware('auth:sanctum');

// Score işlemleri
Route::get('/scores', [ScoreController::class, 'index']);
Route::get('/scores/{range}', [ScoreController::class, 'indexByRange']); // Puan aralığı rotası
Route::post('/scores', [ScoreController::class, 'store']);

// Dosya işlemleri
Route::get('/files', [FileController::class, 'index']);
Route::post('/files', [FileController::class, 'store']);
Route::get('/files/{id}/download', [FileController::class, 'download']);
