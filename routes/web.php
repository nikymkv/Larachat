<?php

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

Route::get('/chat', function () {
    return view('chat');
});

Route::post('laravel-websockets/event', function (Illuminate\Http\Request $request) {    
    App\Events\PublicChat::dispatch($request->input('data'));
});

Route::post('messages', function (Illuminate\Http\Request $request) {
    App\Events\PublicChat::dispatch($request->input('body'));
});

Route::post('private-message', function (Illuminate\Http\Request $request) {
    App\Events\PrivateChat::dispatch($request->input());
});

Route::get('room/{room}', function (App\Models\Room $room) {
    return view('room', ['room' => $room]);
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
