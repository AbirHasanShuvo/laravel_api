<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogCategoryController;
use App\Http\Controllers\API\BlogPostController;
use App\Http\Controllers\API\PeopleApiController;
use App\Http\Controllers\API\StudentApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TestApiController;
use App\Models\BlogCategory;
use App\Models\BlogPost;
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
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes (require token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/logout', [AuthController::class, 'logout']);

    //Blog category routes
    Route::apiResource('categories', BlogCategoryController::class)->middleware(['role:admin']);
    //for post API
    Route::apiResource('posts', BlogPostController::class,)->middleware('role:admin, author');
    Route::post('blog-post-image/{post}', [BlogPostController::class, 'blogPostImage'])->name('blog-post-image')->middleware('role:admin, author');
});

Route::get('categories', [BlogCategoryController::class, 'index']);
Route::get('posts', [BlogPostController::class, 'index'])->name('index');
