@extends('layouts.app')

@section('content')
<div class="container">
    <chats :chats="{{ $chats_json }}"></chats>
</div>
@endsection
