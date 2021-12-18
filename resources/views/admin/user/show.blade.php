@extends('layouts.layout')

@section('content')
    <h1>user informatin ({{ $user_status }})</h1>

    <ul>
        <li>{{ $user->id }}</li>
        <li>{{ $user->name }}</li>
        <li>{{ $user->email }}</li>
        <li>{{ $user->type }}</li>
    </ul>

@endsection
