<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PuanlafotoController;
use App\Http\Controllers\Api\SorularController;
use App\Http\Controllers\Api\ScoreController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\TranscriptionController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\AuthController;

// Kullanıcı bilgilerini getirme
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Media upload rotası
Route::post('/upload', [MediaController::class, 'upload'])->middleware('check.media.upload');

// Auth işlemleri
Route::post('/login', [AuthController::class, 'login']);

// Sorular CRUD işlemleri
Route::prefix('sorular')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SorularController::class, 'index']);
    Route::get('/{id}', [SorularController::class, 'show']);
    Route::post('/', [SorularController::class, 'store']);
    Route::put('/{id}', [SorularController::class, 'update']);
    Route::delete('/{id}', [SorularController::class, 'destroy']);
});

// Member işlemleri
Route::apiResource('members', MemberController::class);

// Score işlemleri
Route::prefix('scores')->group(function () {
    Route::get('/', [ScoreController::class, 'index']);
    Route::get('/{range}', [ScoreController::class, 'indexByRange']); // Puan aralığı
    Route::post('/', [ScoreController::class, 'store']);
});

// Dosya işlemleri
Route::prefix('files')->group(function () {
    Route::get('/', [FileController::class, 'index']);
    Route::post('/', [FileController::class, 'store']);
    Route::get('/{id}/download', [FileController::class, 'download']);
});

// Transcribe işlemleri
Route::prefix('transcribe')->group(function () {
    Route::post('/', [TranscriptionController::class, 'transcribe']);
    Route::post('/upload', [AudioController::class, 'upload']);
});


// Family Member işlemleri
Route::prefix('family')->group(function () {
    Route::get('/', [FamilyMemberController::class, 'index']);
    Route::post('/', [FamilyMemberController::class, 'store']);
    Route::put('/{id}', [FamilyMemberController::class, 'update']);
    Route::delete('/{id}', [FamilyMemberController::class, 'destroy']);
});

// Puanlafoto işlemleri
Route::prefix('puanlafoto')->group(function () {
    Route::post('/yukle', [PuanlafotoController::class, 'yukle']);
    Route::get('/', [PuanlafotoController::class, 'index']);
    Route::post('/', [PuanlafotoController::class, 'store']);
});

// Test rotası
Route::post('/test', function () {
    return response()->json(['message' => 'Test başarılı'], 200);
});
