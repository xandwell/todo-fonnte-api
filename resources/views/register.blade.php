@extends('layout')

@section('title', 'Register')

@section('content')
    <h2>Register</h2>
    <form method="POST" action="/api/register">
        @csrf
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Register</button>
    </form>
@endsection
