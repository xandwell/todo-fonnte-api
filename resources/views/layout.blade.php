<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e8f5e9;
            color: #2e7d32;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #4caf50;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 1rem;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        button {
            background-color: #66bb6a;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #388e3c;
        }
        .completed {
            text-decoration: line-through;
            color: #9e9e9e;
        }
    </style>
</head>
<body>
    <nav>
        <a href="/">Home</a>
        <div>
            @auth
                <a href="#" onclick="logout()">Logout</a>

                <script>
                    async function logout() {
                        const token = localStorage.getItem('token');
                        await fetch('/api/logout', {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`
                            }
                        });
                        localStorage.removeItem('token'); // Clear token
                        window.location.href = '/'; // Redirect to landing page
                    }
                </script>
            @else
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            @endauth
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
