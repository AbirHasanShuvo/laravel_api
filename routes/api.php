<?php

use App\Http\Controllers\API\StudentApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TestApiController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//first api route for testing

Route::get('/test', [TestApiController::class, 'test'])->name('test-api');
Route::resource('students', StudentApiController::class);
