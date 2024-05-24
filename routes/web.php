<?php

use App\Http\Controllers\AltUserController;
use App\Http\Controllers\GoalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::prefix('users')->group(function () {
//    Route::get('/{id}',[AltUserController::class,'getUser']);
//});

Route::prefix('goals')->group(function(){
    Route::post('/create',[GoalController::class,'store']);
});


Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api|public).*$');
