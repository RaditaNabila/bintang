<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bintang Poin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="logo">
                <img src="{{ asset('foto/logo1.png') }}" alt="Logo Bintang Poin">
            </div>
            <h1>Bintang Poin</h1>
            <p>Sistem pengelolaan poin siswa untuk mencatat perilaku positif maupun pelanggaran. Membantu guru melakukan pembinaan dan memberikan apresiasi secara lebih mudah, cepat, dan terstruktur.</p>
            <div class="star-row">★ ★ ★</div>
        </div>
        <div class="login-right">
            <h2>Selamat Datang</h2>
            <p class="subtitle">Silakan login untuk mengakses sistem Bintang Poin.</p>

            {{-- Pesan Alert Error dari Controller --}}
            @if(session('error'))
                <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 14px;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-box">
                        <span class="icon">●</span>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autocomplete="username">
                    </div>
                    @error('username')
                        <small style="color: red; display: block; margin-top: 4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-box">
                        <span class="icon">●</span>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                        <span class="toggle-password" onclick="togglePassword()" id="toggleIcon">👁</span>
                    </div>
                    @error('password')
                        <small style="color: red; display: block; margin-top: 4px;">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="login-btn">Masuk</button>
            </form>

            <div class="footer">
                © 2026 Bintang Poin | Sistem Poin Siswa<br>
                SDIT Nurul Fikri Banjarmasin
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                password.type = 'password';
                toggleIcon.textContent = '👁';
            }
        }
    </script>
</body>
</html>
