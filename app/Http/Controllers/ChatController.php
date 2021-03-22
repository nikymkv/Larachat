<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatMember;
use App\Models\User;
use App\Events\PrivateChat;
use App\Events\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class ChatController extends Controller
{
    /**
     * Get view all chats of an authorized user
     */
    public function index()
    {
        return view('chats');
    }

    /**
     * Get all messages by chat_id
     */
    public function show($chat_id)
    {
        if (Auth::user()->containChat($chat_id)) {
            return view('chat', compact(['chat_id']));
        } else {
            return redirect('/chats');
        }
    }

    /**
     * Get all message chat by chat_id
     */
    public function getAllMessages($chat_id)
    {
        $chat_id = (int)$chat_id;
        $chat = Chat::where('id', '=', $chat_id)->get()->first();
        if ($chat && $chat->members->contains('id', Auth::id())) {
            $response['messages'] = ChatMessage::join('users', 'users.id', '=', 'chat_messages.user_id')
                                    ->where('chat_messages.chat_id', '=', $chat_id)
                                    ->select(['chat_messages.content', 'chat_messages.created_at', 'users.name'])
                                    ->orderBy('chat_messages.created_at', 'asc')
                                    ->get()
                                    ->toArray();

            $response['participant'] = ChatMember::join('users', 'users.id', '=', 'chat_members.user_id')
                                                ->where('chat_members.chat_id', '=', $chat_id)
                                                ->where('users.id', '!=', Auth::id())
                                                ->select(['users.id', 'users.name'])
                                                ->limit(1)
                                                ->get()
                                                ->first();

            $response_json = json_encode($response);
            
            return response()->json($response_json);
        } else {
            return response()->json('Not contains');
        }
    }

    public function getAllChats()
    {
        $chats = Chat::join('chat_members', 'chat_members.chat_id', '=', 'chats.id')
                    ->where('chat_members.user_id', '=', Auth::id())
                    ->select(['chats.id', 'chats.type', 'chats.last_message', 'chat_members.chat_title'])
                    ->get();
        $chats_json = json_encode($chats);

        return response()->json($chats_json);
    }

    /**
     * Save message to database and broadcasting to all members in the chat
     * 
     */
    public function sendMessage(Request $request)
    {
        $input = $request->only([
            'user_id', 'name', 'chat_id', 'content', 'created_at', 'participant'
        ]);

        echo '<pre>' . print_r($input, true) . '</pre>';

        $dateToUTC = Carbon::parse($input['created_at'])->tz(config('app.timezone'));

        $message = ChatMessage::insert([
            'user_id' => $input['user_id'],
            'chat_id' => $input['chat_id'],
            'content' => $input['content'],
            'created_at' => $dateToUTC,
        ]);

        $chat = Chat::where('id', '=', $input['chat_id'])
                    ->update([
                        'last_message' => $input['content']
                    ]);

        broadcast(new PrivateChat($input))->toOthers();
        broadcast(new NewMessage([
            'user_id' => $input['participant']['id'],
            'chat_id' => $input['chat_id'],
            'by_user_id' => $input['user_id'],
            'name' => $input['name'],
            'content' => $input['content'],
            'created_at' => $dateToUTC,
        ]))->toOthers();
    }

    /**
     * Remove selected message from database (maybe soft delete)
     */
    public function destroy(Chat $chat)
    {
        //
    }
}
