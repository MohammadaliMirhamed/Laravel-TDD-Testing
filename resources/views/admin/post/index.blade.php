@extends('layouts.layout')

@section('content')
    <h1>posts list</h1>

    <ul>
        @foreach($posts as $post)
            <li>{{ $post->title }}</li>
            <li> edit </li>
            <li> delete</li>
        @endforeach
    </ul>

@endsection
