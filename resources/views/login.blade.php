@extends('layout')

@section('title', 'Login')

@section('content')
    <h2>Login</h2>
    <form id="login-form">
        @csrf
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>

    <script>
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            const response = await fetch('/api/login', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                localStorage.setItem('token', data.token); // Store token
                window.location.href = '/home'; // Redirect to home page
            } else {
                alert('Invalid login!');
            }
        });
    </script>
@endsection
