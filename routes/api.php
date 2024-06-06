<?php

use App\Http\Controllers\AltUserController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
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


Route::prefix('users')->group(function () {
    Route::get('/{id}',[AltUserController::class,'getUser']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




    Route::post('/register',[UserController::class,'store']);
    Route::post('/login',[UserController::class,'login']);
    Route::post('/logout',[UserController::class,'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('goals')->group(function(){
        Route::get('/', [GoalController::class, 'index']);
        Route::get('/{id}', [GoalController::class, 'getGoal']);
        Route::put('/{id}/update', [GoalController::class, 'update']);
        Route::delete('/{id}/delete', [GoalController::class, 'delete']);
        Route::post('/create',[GoalController::class,'store']);
    });

    Route::prefix('balances')->group(function(){
        Route::get('/', [BalanceController::class, 'index']);
        Route::get('/incomes', [BalanceController::class, 'income']);
        Route::get('/expenses', [BalanceController::class, 'expense']);
        Route::post('/create',[BalanceController::class,'store']);
        Route::post('/dashboard/{year}',[BalanceController::class,'getBalancesByYear']);
        Route::get('/dashboard/stats/{month}',[BalanceController::class,'expenseTrack']);
        Route::put('/{id}/update',[BalanceController::class,'update']);
        Route::delete('/{id}/delete',[BalanceController::class,'delete']);
    });

    Route::prefix('schedules')->group(function(){
        Route::get('/', [ScheduleController::class, 'index']);
        Route::post('/create',[ScheduleController::class,'store']);
        Route::put('/{id}/update',[ScheduleController::class,'update']);
        Route::delete('/{id}/delete',[ScheduleController::class,'delete']);
    });

    Route::prefix('categories')->group(function(){
        Route::get('/',[CategoriesController::class,'index']);
    });
});

