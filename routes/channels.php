<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
// Messages
Broadcast::channel('chat.{chat_id}', function ($user, $chat_id) {
    if ($user->containChat($chat_id)) {
        return $user;
    }
});

// Notifications
Broadcast::channel('user.{user_id}', function ($user, $chat_id) {
    // if ($user->containChat($chat_id)) {
    //     return $user;
    // }
    return true;
});
// Broadcast::channel('events', function ($user) {
//     // if ($user->containChat($chat_id)) {
//     //     return $user;
//     // }
//     return true;
// });
