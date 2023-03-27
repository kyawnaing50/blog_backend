<?php
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CategoryController;


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

Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);

Route::middleware(['auth:api'])->group(function() {

    Route::post('logout',[AuthController::class,'logout']);

    //Profile
    Route::get('profile',[ProfileController::class,'profile']);
    Route::get('profile-posts',[ProfileController::class,'posts']);
    //categories
    Route::get('categories',[CategoryController::class, 'index']);
    //Post
    Route::get('post',[PostController::class, 'index']);
    Route::post('post',[PostController::class, 'create']);
    Route::get('post/{id}',[PostController::class,'show']);
});

