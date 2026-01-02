<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Presensi PKL</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-card">
    <div class="header">
        <h1>Login Admin</h1>
        <p>Silakan masuk untuk mengelola data</p>
    </div>

    <div id="errorBox" class="alert alert-error"></div>

    <form id="loginForm">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" placeholder="Masukkan username admin" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" placeholder="••••••••" required>
        </div>

        <button type="submit" id="btnSubmit">Masuk</button>
    </form>
</div>

<script>
    if(localStorage.getItem('api_token')) {
        window.location.href = '/dashboard';
    }

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = document.getElementById('btnSubmit');
        const errorBox = document.getElementById('errorBox');

        btn.innerText = 'Memproses...';
        btn.disabled = true;
        errorBox.style.display = 'none';

        const payload = {
            username: document.getElementById('username').value,
            password: document.getElementById('password').value
        };

        try {
            const response = await fetch('/api/users/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                localStorage.setItem('api_token', data.token);
                localStorage.setItem('user_name', data.data.nama);
                window.location.href = '/dashboard';
            } else {
                throw new Error(data.message || 'Login gagal.');
            }

        } catch (error) {
            errorBox.innerText = error.message;
            errorBox.style.display = 'block';
            btn.innerText = 'Masuk';
            btn.disabled = false;
        }
    });
</script>
</body>
</html>
