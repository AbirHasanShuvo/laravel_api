<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PeopleApiController;
use App\Http\Controllers\API\StudentApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TestApiController;
use App\Models\Student;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//first api route for testing

Route::get('/test', [TestApiController::class, 'test'])->name('test-api');
Route::apiResource('/students', StudentApiController::class);
// Route::get('students', [StudentApiController::class, 'index']);
Route::apiResource('/peoples', PeopleApiController::class);

//new routes

// Public routes (NO middleware)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
});
