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


Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api|public|build).*$');
