<?php

use App\Http\Controllers\XMController;
use App\Mail\XMMail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/historical-data', [XMController::class, 'historicalData']);
Route::post('/submit', [XMController::class, 'submit']);
