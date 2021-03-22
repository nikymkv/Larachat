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


// Route::get('/chat', function () {
//     return view('chat');
// });

// Route::post('laravel-websockets/event', function (Illuminate\Http\Request $request) {    
//     App\Events\PublicChat::dispatch($request->input('data'));
// });

// Route::post('messages', function (Illuminate\Http\Request $request) {
//     App\Events\PublicChat::dispatch($request->input('body'));
// });

// Route::post('private-message', function (Illuminate\Http\Request $request) {
//     App\Events\PrivateChat::dispatch($request->input());
// });

// Route::get('room/{room}', function (App\Models\Room $room) {
//     return view('room', ['room' => $room]);
// });


Auth::routes();

Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);

Route::middleware(['auth'])->group(function () {
    // Chats
    Route::get('chats', [App\Http\Controllers\ChatController::class, 'index']);
    Route::get('chats/fetch-all', [App\Http\Controllers\ChatController::class, 'getAllChats']);
    Route::post('chats/messages', [App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::get('chats/{chat_id}', [App\Http\Controllers\ChatController::class, 'show']);
    Route::get('chats/{chat_id}/messages', [App\Http\Controllers\ChatController::class, 'getAllMessages']);
});