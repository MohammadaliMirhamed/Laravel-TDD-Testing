@extends('layouts.layout')

@section('content')
    <h1>{{ $post->title }}</h1>

    <ul>
        @foreach($comments as $comment)
            <li>{{ $comment->text }}</li>
        @endforeach
    </ul>

    @auth
        <form action="{{ route('single.comment', $post->id) }}" method="POST">
            <textarea name="text"></textarea>
        </form>
    @endauth
@endsection
