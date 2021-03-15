@extends('layouts.app')

@section('content')
    <div class="container">
        <chat :user="{{ auth()->user() }}" :chat_id="{{ $chat_id }}"></chat>
    </div>
@endsection