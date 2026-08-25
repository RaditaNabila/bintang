<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pilih Peran - Bintang Poin</title>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-amber-400 via-orange-400 to-amber-500 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-8 space-y-8 text-center">

        {{-- HEADER --}}
        <div class="space-y-3">
            <div class="w-20 h-20 bg-gradient-to-tr from-amber-500 to-orange-500 text-white rounded-3xl flex items-center justify-center mx-auto shadow-lg shadow-amber-500/30 transform transition hover:rotate-6">
                <i class="fa-solid fa-star text-4xl"></i>
            </div>

            <h1 class="text-2xl font-bold text-gray-800">
                Selamat Datang
            </h1>

            <p class="text-xs text-gray-500 max-w-xs mx-auto">
                Silakan pilih jenis akses Anda untuk melanjutkan ke aplikasi Bintang Poin
            </p>
        </div>

        {{-- PILIH PERAN --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- GURU / STAFF --}}
            <a href="{{ route('guru.dashboard') }}"
               class="group flex flex-col items-center p-6 border-2 border-amber-100 bg-amber-50/40 rounded-2xl transition-all duration-300 hover:border-amber-500 hover:bg-amber-50 hover:shadow-xl hover:-translate-y-1">
                <div class="w-16 h-16 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl mb-4 shadow-md group-hover:scale-110 transition duration-300">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>

                <h3 class="font-bold text-gray-800 text-base mb-1">
                    Guru / Staff
                </h3>

                <p class="text-[11px] text-gray-500 leading-relaxed">
                    Kelola poin &amp; data siswa
                </p>
            </a>

            {{-- WALI MURID --}}
           <a href="{{ route('wali') }}"
            class="group flex flex-col items-center p-6 border-2 border-amber-100 bg-amber-50/40 rounded-2xl transition-all duration-300 hover:border-amber-500 hover:bg-amber-50 hover:shadow-xl hover:-translate-y-1">
                <div class="w-16 h-16 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-2xl mb-4 shadow-md group-hover:scale-110 transition duration-300">
                    <i class="fa-solid fa-users"></i>
                </div>

                <h3 class="font-bold text-gray-800 text-base mb-1">
                    Wali Murid
                </h3>

                <p class="text-[11px] text-gray-500 leading-relaxed">
                    Lihat laporan poin anak
                </p>
            </a>
        </div>

        {{-- FOOTER --}}
        <p class="text-[11px] text-gray-400">
            © 2026 Bintang Poin | SDIT Nurul Fikri
        </p>

    </div>

</body>

</html>
