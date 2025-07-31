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
    Route::get('/{id}',[UserController::class,'getUser']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
    Route::post('/register',[UserController::class,'store']);
    Route::post('/login',[UserController::class,'login']);
    Route::post('/logout',[UserController::class,'logout']);
    Route::post('/demo',[GoalController::class,'demo']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('goals')->group(function(){
        Route::get('/', [GoalController::class, 'index']);
        Route::get('/{id}', [GoalController::class, 'getGoal']);
        Route::get('/checkGoal', [GoalController::class, 'checkGoal']);
        Route::put('/{id}/update', [GoalController::class, 'update']);
        Route::delete('/{id}/delete', [GoalController::class, 'delete']);
        Route::post('/create',[GoalController::class,'store']);
        Route::post('/finish',[GoalController::class,'finishGoal']);
    });

    Route::prefix('balances')->group(function(){
        Route::get('/', [BalanceController::class, 'index']);
        Route::get('/incomes', [BalanceController::class, 'income']);
        Route::get('/expenses', [BalanceController::class, 'expense']);
        Route::post('/create',[BalanceController::class,'store']);
        Route::post('/dashboard/pie/{year}',[BalanceController::class,'realPieData']);
        Route::post('/month',[BalanceController::class,'getMonthInfo']);
        Route::get('/profile/{id}',[BalanceController::class,'calculateBalances']);
        Route::post('/profile/budget',[UserController::class,'updateBudget']);
        Route::post('/monthlyBalances',[BalanceController::class,'getMonthBalances']);
        Route::get('/dashboard/stats/{month}',[BalanceController::class,'expenseTrack']);
        Route::post('/dashboard/percentage',[BalanceController::class,'getCategoriesData']);
        Route::post('/dashboard/{year}',[BalanceController::class,'getBalancesByYear']);
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
        Route::get('/sub',[CategoriesController::class,'subCategories']);
        Route::get('/inc',[CategoriesController::class,'incCategories']);
        Route::get('/exp',[CategoriesController::class,'expCategories']);
        Route::post('/save',[CategoriesController::class,'savePresets']);
    });
});

