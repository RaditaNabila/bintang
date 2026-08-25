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
            <form>
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-box">
                        <span class="icon">●</span>
                        <input type="text" placeholder="Masukkan username">
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-box">
                        <span class="icon">●</span>
                        <input type="password" id="password" placeholder="Masukkan password">
                        <span class="toggle-password" onclick="togglePassword()" id="toggleIcon">👁</span>
                    </div>
                </div>
              <!-- Tombol Masuk langsung ke Dashboard Guru -->
                <a href="{{ route('pilih-peran') }}" class="login-btn">Masuk</a>
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
