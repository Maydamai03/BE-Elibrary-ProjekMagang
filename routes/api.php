<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookController;

// Rute Publik untuk Otentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute yang Dilindungi Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rute Buku yang bisa diakses semua role (admin & pembaca)
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);

    // Rute Buku yang HANYA bisa diakses oleh ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        // Gunakan POST untuk update agar mudah dari sisi frontend (Flutter)
        Route::post('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
    });
});
