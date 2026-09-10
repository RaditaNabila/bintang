@extends('layouts.app')

@section('title', 'Informasi Pengembang - Bintang Poin')
@section('page_title', 'Informasi Pengembang & Pusat Bantuan')
@section('page_description', 'Profil tim pembuat sistem, layanan kendala teknis dan informasi layanan')

@section('content')

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-8px);
        }
    }

    @keyframes pulseGlow {
        0%, 100% {
            opacity: 0.2;
            transform: scale(1);
        }
        50% {
            opacity: 0.4;
            transform: scale(1.05);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .animate-float {
        animation: float 4s ease-in-out infinite;
    }

    .animate-pulse-glow {
        animation: pulseGlow 3s infinite ease-in-out;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.35s; }
    .delay-4 { animation-delay: 0.5s; }
    .delay-5 { animation-delay: 0.65s; }
</style>

<div class="space-y-8 max-w-5xl mx-auto w-full">

    {{-- 1. BANNER --}}
    <div class="opacity-0 animate-fade-in delay-1 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden transform hover:-translate-y-1 transition duration-300">
        <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/20 rounded-full blur-2xl animate-pulse-glow"></div>

        <div class="relative z-10 max-w-2xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-white/25 rounded-full text-[11px] font-semibold tracking-wider uppercase backdrop-blur-md">
                <i class="fa-solid fa-sparkles text-amber-200"></i>
                Official Developer Team
            </div>

            <h3 class="text-2xl font-bold leading-tight">
                Terima Kasih Telah Menggunakan Sistem Bintang Poin! 🙌
            </h3>

            <p class="text-xs text-amber-100 leading-relaxed">
                Sistem <strong>Bintang Poin</strong> dirancang dan dibangun untuk membantu sekolah dalam mengelola poin prestasi, pelanggaran, data siswa, serta berbagai informasi terkait perkembangan siswa secara lebih terstruktur dan mudah digunakan.
            </p>
        </div>

        <i class="fa-solid fa-code-merge text-white/10 text-9xl absolute -right-4 -bottom-6 pointer-events-none animate-float"></i>
    </div>

    {{-- 2. BUKU PANDUAN & VIDEO TUTORIAL --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- MANUAL BOOK --}}
        <div class="opacity-0 animate-fade-in delay-2 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-6 text-white shadow-lg flex flex-col justify-between transform hover:-translate-y-1 transition duration-300">

            <div class="flex items-start gap-4 mb-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/20">
                    <i class="fa-solid fa-file-pdf text-3xl text-rose-300 animate-float"></i>
                </div>

                <div class="space-y-1">
                    <h4 class="font-bold text-base leading-tight">
                        Buku Panduan Pengguna (Manual Book)
                    </h4>

                    <p class="text-xs text-blue-100 leading-relaxed">
                        Unduh panduan lengkap penggunaan aplikasi Bintang Poin dalam format PDF untuk memudahkan pengisian poin dan pengelolaan data.
                    </p>
                </div>
            </div>

            <div class="shrink-0 pt-2">
                <a href="{{ asset('assets/pdf/manual-book-bintang-poin.pdf') }}"
                   download
                   target="_blank"
                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-blue-700 hover:bg-blue-50 text-xs font-bold rounded-2xl shadow-md hover:shadow-lg transition transform hover:scale-105">

                    <i class="fa-solid fa-download text-sm"></i>
                    Unduh Panduan (PDF)
                </a>
            </div>
        </div>

        {{-- VIDEO TUTORIAL --}}
        <div class="opacity-0 animate-fade-in delay-2 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-6 text-white shadow-lg flex flex-col justify-between transform hover:-translate-y-1 transition duration-300">

            <div class="flex items-start gap-4 mb-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/20">
                    <i class="fa-solid fa-circle-play text-3xl text-amber-300 animate-float"></i>
                </div>

                <div class="space-y-1">
                    <h4 class="font-bold text-base leading-tight">
                        Video Tutorial Penggunaan Web
                    </h4>

                    <p class="text-xs text-indigo-100 leading-relaxed">
                        Saksikan video panduan langkah demi langkah penggunaan fitur-fitur di dalam sistem Bintang Poin secara visual.
                    </p>
                </div>
            </div>

            <div class="shrink-0 pt-2">
                <a href="https://youtube.com"
                   target="_blank"
                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-indigo-700 hover:bg-indigo-50 text-xs font-bold rounded-2xl shadow-md hover:shadow-lg transition transform hover:scale-105">

                    <i class="fa-solid fa-play text-sm"></i>
                    Tonton Video Tutorial
                </a>
            </div>
        </div>

    </div>

    {{-- 3. PUSAT BANTUAN --}}
    <div class="opacity-0 animate-fade-in delay-2 bg-gradient-to-br from-rose-50 to-orange-50 rounded-3xl border border-orange-200/80 p-6 shadow-sm space-y-4">

        <div class="flex items-center gap-3 border-b border-orange-200/60 pb-3">

            <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-bold shadow-md shrink-0">
                <i class="fa-solid fa-circle-question text-lg animate-float"></i>
            </div>

            <div>
                <h4 class="font-bold text-sm text-gray-800">
                    Ada Pertanyaan, Kendala, atau Permasalahan Sistem?
                </h4>

                <p class="text-xs text-gray-500">
                    Jika menemukan bug, data tidak sesuai, atau kesulitan dalam menggunakan aplikasi, silakan hubungi tim pengembang di bawah.
                </p>
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- WHATSAPP --}}
            <div class="bg-white p-4 rounded-2xl border border-orange-100 shadow-sm flex flex-col justify-between space-y-2">

                <div class="flex items-center gap-2.5 text-xs font-bold text-gray-800">
                    <i class="fa-brands fa-whatsapp text-emerald-500 text-base"></i>
                    <span>Respon Cepat (WhatsApp)</span>
                </div>

                <p class="text-[11px] text-gray-500 leading-relaxed">
                    Konsultasi langsung dengan pengembang untuk perbaikan bug atau kendala sistem.
                </p>

                <div class="inline-flex items-center justify-center gap-1.5 py-2 px-3 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-xl">
                    <i class="fa-solid fa-headset"></i>
                    Direct Chat Available
                </div>

            </div>

            {{-- DUKUNGAN --}}
            <div class="bg-white p-4 rounded-2xl border border-orange-100 shadow-sm flex flex-col justify-between space-y-2">

                <div class="flex items-center gap-2.5 text-xs font-bold text-gray-800">
                    <i class="fa-solid fa-shield-halved text-blue-500 text-base"></i>
                    <span>Dukungan Pemeliharaan</span>
                </div>

                <p class="text-[11px] text-gray-500 leading-relaxed">
                    Dukungan pemeliharaan teknis siap membantu menjaga sistem tetap berjalan optimal.
                </p>

                <span class="inline-flex items-center justify-center gap-1 py-2 px-3 bg-blue-50 text-blue-700 text-[11px] font-bold rounded-xl">
                    <i class="fa-solid fa-check-circle"></i>
                    Technical Support
                </span>

            </div>

            {{-- JAM LAYANAN --}}
            <div class="bg-white p-4 rounded-2xl border border-orange-100 shadow-sm flex flex-col justify-between space-y-2">

                <div class="flex items-center gap-2.5 text-xs font-bold text-gray-800">
                    <i class="fa-solid fa-clock text-amber-500 text-base"></i>
                    <span>Jam Operasional Team</span>
                </div>

                <p class="text-[11px] text-gray-500 leading-relaxed">
                    Senin - Sabtu: 08.00 - 20.00 WITA<br>
                    Kendala mendesak dapat disampaikan kapan saja.
                </p>

                <span class="inline-flex items-center justify-center gap-1 py-2 px-3 bg-amber-50 text-amber-700 text-[11px] font-bold rounded-xl">
                    <i class="fa-solid fa-bolt"></i>
                    Fast Response
                </span>

            </div>

        </div>
    </div>

    {{-- 4. PROFIL DEVELOPER --}}
    <div class="space-y-3">

        <h4 class="opacity-0 animate-fade-in delay-3 text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="fa-solid fa-laptop-code text-amber-500"></i>
            Kontak Langsung Tim Pengembang
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- NABILA --}}
            <div class="opacity-0 animate-fade-in delay-3 bg-white rounded-3xl border border-amber-100/80 p-6 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1.5 group flex flex-col justify-between">

                <div class="flex items-center gap-4 mb-4">

                    <div class="relative">

                        <div class="w-20 h-20 rounded-2xl ring-4 ring-amber-400/30 p-1 bg-white shadow-md overflow-hidden group-hover:ring-amber-500 transition duration-300">
                            <img src="{{ asset('foto/nabila.png') }}"
                                 alt="Radita Nabila Shofa"
                                 class="w-full h-full object-cover rounded-xl group-hover:scale-110 transition duration-500">
                        </div>

                        <span class="absolute -bottom-1 -right-1 bg-emerald-500 border-2 border-white w-4 h-4 rounded-full"
                              title="Online"></span>

                    </div>

                    <div>
                        <h5 class="font-bold text-base text-gray-800 group-hover:text-amber-600 transition">
                            Radita Nabila Shofa
                        </h5>

                        <p class="text-xs font-medium text-amber-600">
                            Frontend & UI/UX Designer
                        </p>
                    </div>

                </div>

                <a href="https://wa.me/6287704490202?text=Halo%20Kak%20Nabila,%20saya%20ingin%20bertanya%20tentang%20sistem%20Bintang%20Poin"
                   target="_blank"
                   class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold shadow-md transition-all duration-300 flex items-center justify-center gap-2">

                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    Chat WhatsApp Nabila
                </a>

            </div>

            {{-- ISMATUL HAWA --}}
            <div class="opacity-0 animate-fade-in delay-4 bg-white rounded-3xl border border-amber-100/80 p-6 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1.5 group flex flex-col justify-between">

                <div class="flex items-center gap-4 mb-4">

                    <div class="relative">

                        <div class="w-20 h-20 rounded-2xl ring-4 ring-orange-400/30 p-1 bg-white shadow-md overflow-hidden group-hover:ring-orange-500 transition duration-300">
                            <img src="{{ asset('foto/isma.jpeg') }}"
                                 alt="Ismatul Hawa"
                                 class="w-full h-full object-cover rounded-xl group-hover:scale-110 transition duration-500">
                        </div>

                        <span class="absolute -bottom-1 -right-1 bg-emerald-500 border-2 border-white w-4 h-4 rounded-full"
                              title="Online"></span>

                    </div>

                    <div>
                        <h5 class="font-bold text-base text-gray-800 group-hover:text-orange-600 transition">
                            Ismatul Hawa
                        </h5>

                        <p class="text-xs font-medium text-orange-600">
                            Backend & Logic Developer
                        </p>
                    </div>

                </div>

                <a href="https://wa.me/6282250216753?text=Halo%20Kak%20Isma,%20saya%20ingin%20bertanya%20tentang%20sistem%20Bintang%20Poin"
                   target="_blank"
                   class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold shadow-md transition-all duration-300 flex items-center justify-center gap-2">

                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    Chat WhatsApp Isma
                </a>

            </div>

        </div>
    </div>

    {{-- 5. PROMOSI PROJECT --}}
    <div class="opacity-0 animate-fade-in delay-5 bg-white rounded-3xl border border-amber-100 p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">

        <div class="space-y-1 text-center md:text-left">

            <h5 class="text-sm font-bold text-gray-800 flex items-center justify-center md:justify-start gap-2">
                <i class="fa-solid fa-rocket text-amber-500 animate-float"></i>
                Mau Buat Sistem atau Web Lagi?
            </h5>

            <p class="text-xs text-gray-500 leading-relaxed max-w-xl">
                Kami melayani pembuatan sistem informasi sekolah, company profile, e-commerce, hingga aplikasi custom sesuai kebutuhan dengan desain modern dan responsif.
            </p>

        </div>

        <div class="shrink-0">

            <a href="https://wa.me/6287704490202?text=Halo%20Kak%20Radita,%20saya%20tertarik%20untuk%20konsultasi%20pembuatan%20web%20baru"
               target="_blank"
               class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-2xl text-xs font-bold shadow-md hover:shadow-lg transition transform hover:scale-105">

                <i class="fa-solid fa-comments mr-1.5"></i>
                Konsultasi Project Baru
            </a>

        </div>
    </div>

    {{-- FOOTER --}}
    <p class="text-center text-[11px] text-gray-400 pt-2 pb-4">
        &copy; {{ date('Y') }} <strong>Bintang Poin System</strong>. Developed with ❤️ by Our Team.
    </p>

</div>

@endsection