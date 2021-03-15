<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Использую методы Laravel
        // $chats = User::where('id', '=', Auth::id())->get()->first()->userChats()->select(['id', 'type', 'title', 'last_message'])->get()->toArray();
        // $chats_json = json_encode($chats);
        // return response()->json($chats_json);
        // return view('chats', compact(['chats_json']));

        $chats = Chat::join('chat_members', 'chat_members.chat_id', '=', 'chats.id')
                    ->where('chat_members.user_id', '=', Auth::id())
                    ->select(['chats.id', 'chats.type', 'chats.last_message', 'chat_members.chat_title'])
                    ->get();
        $chats_json = json_encode($chats);

        return view('chats', compact(['chats_json']));
    }

    public function getAllMessages($chat_id)
    {
        $chat_id = (int)$chat_id;
        $chat = Chat::where('id', '=', $chat_id)->get()->first();
        if ($chat && $chat->members->contains('id', Auth::id())) {
            $messages = ChatMessage::join('users', 'users.id', '=', 'chat_messages.user_id')
                                    ->where('chat_messages.chat_id', '=', $chat_id)
                                    ->select(['chat_messages.content', 'chat_messages.created_at', 'users.name'])
                                    ->orderBy('chat_messages.created_at', 'asc')
                                    ->get()
                                    ->toArray();
            $messages_json = json_encode($messages);
            // return view('chat', compact(['messages_json']));
            return response()->json($messages_json);
        } else {
            return response()->json('Not contains');
        }
    }

    public function sendMessage()
    {
        return 0;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        App\Events\PrivateChat::dispatch($request->input());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */

    // Поправить проверку на доступ к чату (1 и 2 запрос)
    public function show(Request $request, $chat_id)
    {
        // $chat_id = (int)$chat;
        // $chat = Chat::where('id', '=', $chat_id)->get()->first();
        // if ($chat && $chat->members->contains('id', Auth::id())) {
        //     $messages = ChatMessage::join('users', 'users.id', '=', 'chat_messages.user_id')
        //                             ->where('chat_messages.chat_id', '=', $chat_id)
        //                             ->select(['chat_messages.content', 'chat_messages.created_at', 'users.name'])
        //                             ->orderBy('chat_messages.created_at', 'asc')
        //                             ->get()
        //                             ->toArray();
        //     $messages_json = json_encode($messages);
        //     return view('chat', compact(['messages_json']));
        // } else {
        //     return response()->json('Not contains');
        // }

        return view('chat', compact(['chat_id']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function edit(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chat $chat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chat $chat)
    {
        //
    }
}
