<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Sandi - Bintang Poin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .login-right {
            padding: 25px 35px !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-right h2 {
            font-size: 20px !important;
            margin-bottom: 4px !important;
        }
        .login-right .subtitle {
            font-size: 12px !important;
            margin-bottom: 12px !important;
        }
        .form-group {
            margin-bottom: 10px !important;
        }
        .form-group label {
            font-size: 11px !important;
            margin-bottom: 2px !important;
            display: block;
        }

        /* Memperbaiki posisi kotak input dan ikon titik supaya tidak menimpa teks */
        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-box .icon {
            position: absolute;
            left: 12px;
            color: #9ca3af;
            font-size: 10px;
            pointer-events: none;
        }
        .input-box input {
            width: 100%;
            padding: 8px 10px 8px 30px !important; /* Padding kiri ditambah agar teks tidak menabrak ikon */
            font-size: 12px !important;
        }

        .login-btn {
            padding: 10px !important;
            margin-top: 5px !important;
            margin-bottom: 8px !important;
            font-size: 13px !important;
        }
        .footer {
            margin-top: 10px !important;
            font-size: 10px !important;
        }
    </style>
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
            <h2>Reset Sandi</h2>
            <p class="subtitle">Masukkan username Anda dan buat password baru.</p>

            {{-- Kotak Alert Sukses Buatan --}}
            <div id="alertSukses" style="display: none; color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 6px; border-radius: 4px; margin-bottom: 10px; text-align: center; font-size: 12px;">
                Password berhasil diubah! (Mode Tampilan Saja)
            </div>

            <form id="formGantiSandi" onsubmit="simpanPalsu(event)">
                <div class="form-group">
                    <label>Username Akun</label>
                    <div class="input-box">
                        <span class="icon">●</span>
                        <input type="text" name="username" placeholder="Masukkan username Anda" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password Baru</label>
                    <div class="input-box">
                        <span class="icon">●</span>
                        <input type="password" name="password" placeholder="Masukkan password baru" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <div class="input-box">
                        <span class="icon">●</span>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" required>
                    </div>
                </div>

                <button type="submit" class="login-btn">Simpan Password Baru</button>
            </form>

            <div style="text-align: center; margin-top: 5px;">
                <a href="{{ route('login') }}" style="font-size: 11px; color: #6b7280; text-decoration: none; font-weight: 500;">
                    ← Kembali ke Halaman Login
                </a>
            </div>

            <div class="footer">
                © 2026 Bintang Poin | Sistem Poin Siswa<br>
                SDIT Nurul Fikri Banjarmasin
            </div>
        </div>
    </div>

    <script>
        function simpanPalsu(event) {
            event.preventDefault();
            document.getElementById('alertSukses').style.display = 'block';
            setTimeout(() => {
                document.getElementById('formGantiSandi').reset();
            }, 1500);
        }
    </script>
</body>
</html>
