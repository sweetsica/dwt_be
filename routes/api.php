<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\TargetController;
use App\Http\Controllers\Api\TargetDetailController;
use App\Http\Controllers\Api\TargetLogController;
use App\Http\Controllers\Api\UserController;

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

// api/v1/{resource}
Route::group(['prefix' => 'v1'], function () {
    Route::get('health', [HealthController::class, 'index']);
    //these for testing role authorization remove them in production
    Route::get('admin', [HealthController::class, 'admin'])->middleware('auth.role:admin');
    Route::get('user', [HealthController::class, 'user'])->middleware('auth.role:user,admin');
    //auth api this group has middleware in controller constructor
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });

    //users api only admin can access
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        // Route::put('/{id}', [UserController::class, 'update']);
        // Route::delete('/{id}', [UserController::class, 'destroy']);
    })->middleware('auth.role:admin');

    //target api
    Route::apiResource('targets', TargetController::class)->middleware('auth.role:manager,admin');
    Route::apiResource('target-details', TargetDetailController::class)->middleware('auth.role:manager,admin');
    Route::apiResource('target-logs', TargetLogController::class)->middleware('auth.role:user,manager,admin');
});
