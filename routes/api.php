<?php

use App\Http\Controllers\AltUserController;
use App\Http\Controllers\GoalController;
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

Route::prefix('goals')->group(function(){
    Route::get('/', [GoalController::class, 'index']);
    Route::get('/{id}', [GoalController::class, 'getGoal']);
    Route::put('/{id}/update', [GoalController::class, 'update']);
    Route::delete('/{id}/update', [GoalController::class, 'update']);
    Route::post('/create',[GoalController::class,'store']);
});
