</html>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Absen — Platform kehadiran untuk institusi Indonesia</title>
    <meta name="description"
        content="Smart Absen membantu perusahaan, TPQ, sekolah, dan madrasah mencatat kehadiran dengan rapi — lalu mengubahnya menjadi keputusan yang lebih cepat.">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%23102b69%22/><text x=%2250%25%22 y=%2258%25%22 font-size=%2255%22 text-anchor=%22middle%22 fill=%22%23f6b51e%22>S</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="noise" style="min-height:100dvh; overflow-x:hidden;">
    <script>
    // Data lembaga mitra asli dari database (dipakai script.js untuk mengisi #directory-grid)
    window.TENANT_DIRECTORY = @json($directoryData);
    </script>

    <!-- ======================= HEADER ======================= -->
    <header id="site-header"
        class="fixed left-0 right-0 top-0 z-40 border-b border-[#e5ebf4]/80 bg-white/95 backdrop-blur-md">
        <div class="section-wrap flex h-[74px] items-center justify-between">
            <a href="#beranda" class="flex items-center gap-2.5">
                <span
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f6b51e] text-[#122e70] shadow-[0_8px_20px_rgba(246,181,30,.25)]">
                    <i data-lucide="calendar-check-2" style="width:19px;height:19px;stroke-width:2.5"></i>
                </span>
                <span class="text-[17px] font-extrabold tracking-[-.06em] text-[#102b69]">smart<span
                        class="text-[#f0a914]">absen</span></span>
            </a>

            <nav class="hidden items-center gap-5 xl:flex" aria-label="Navigasi utama" id="desktop-nav">
                <a href="#beranda" class="nav-link nav-active text-[11px] font-semibold" data-nav="Beranda">Beranda</a>
                <a href="#tentang" class="nav-link text-[11px] font-semibold text-[#607591]"
                    data-nav="Tentang">Tentang</a>
                <a href="#fitur" class="nav-link text-[11px] font-semibold text-[#607591]" data-nav="Fitur">Fitur</a>
                <a href="#website-mitra" class="nav-link text-[11px] font-semibold text-[#607591]"
                    data-nav="Website Mitra">Website Mitra</a>
                <a href="#cara-kerja" class="nav-link text-[11px] font-semibold text-[#607591]"
                    data-nav="Cara Kerja">Cara Kerja</a>
                <a href="#harga" class="nav-link text-[11px] font-semibold text-[#607591]" data-nav="Harga">Harga</a>
                <a href="#faq" class="nav-link text-[11px] font-semibold text-[#607591]" data-nav="FAQ">FAQ</a>
            </nav>

            <div class="hidden items-center gap-2 md:flex">
                <a href="{{ route('login.form') }}"
                    class="rounded-lg border border-[#cad8eb] px-3.5 py-2 text-[12px] font-bold text-[#244c96] transition hover:border-[#2563eb]">Masuk</a>
                <a href="{{ route('register.form') }}"
                    class="shine-button button-lift rounded-lg bg-[#2563eb] px-4 py-2.5 text-[12px] font-bold text-white inline-flex items-center">Daftar
                    <i data-lucide="arrow-up-right" style="width:13px;height:13px;margin-left:4px"></i></a>
            </div>

            <button type="button" id="mobile-menu-btn"
                class="rounded-lg border border-[#d4e0ee] p-2 text-[#1e4b9b] md:hidden" aria-label="Buka menu">
                <i data-lucide="menu" id="icon-menu-open" style="width:20px;height:20px"></i>
                <i data-lucide="x" id="icon-menu-close" style="width:20px;height:20px;display:none"></i>
            </button>
        </div>

        <div id="mobile-menu" class="hidden border-t border-[#e5ebf4] bg-white px-5 pb-5 pt-3 shadow-lg md:hidden">
            <div class="flex flex-col">
                <a href="#beranda"
                    class="mobile-nav-link border-b border-[#edf1f6] py-3 text-sm font-semibold text-[#2563eb]"
                    data-nav="Beranda">Beranda</a>
                <a href="#tentang"
                    class="mobile-nav-link border-b border-[#edf1f6] py-3 text-sm font-semibold text-[#526a88]"
                    data-nav="Tentang">Tentang</a>
                <a href="#fitur"
                    class="mobile-nav-link border-b border-[#edf1f6] py-3 text-sm font-semibold text-[#526a88]"
                    data-nav="Fitur">Fitur</a>
                <a href="#website-mitra"
                    class="mobile-nav-link border-b border-[#edf1f6] py-3 text-sm font-semibold text-[#526a88]"
                    data-nav="Website Mitra">Website Mitra</a>
                <a href="#cara-kerja"
                    class="mobile-nav-link border-b border-[#edf1f6] py-3 text-sm font-semibold text-[#526a88]"
                    data-nav="Cara Kerja">Cara Kerja</a>
                <a href="#harga"
                    class="mobile-nav-link border-b border-[#edf1f6] py-3 text-sm font-semibold text-[#526a88]"
                    data-nav="Harga">Harga</a>
                <a href="#faq"
                    class="mobile-nav-link border-b border-[#edf1f6] py-3 text-sm font-semibold text-[#526a88]"
                    data-nav="FAQ">FAQ</a>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('login.form') }}"
                        class="flex-1 rounded-lg border border-[#cad8eb] py-3 text-center text-sm font-bold text-[#244c96]">Masuk</a>
                    <a href="{{ route('register.form') }}"
                        class="flex-1 rounded-lg bg-[#2563eb] py-3 text-center text-sm font-bold text-white">Daftar</a>
                </div>
            </div>
        </div>
    </header>

    <!-- ======================= HERO ======================= -->
    <section id="beranda"
        class="mesh-blue relative overflow-hidden bg-[#102b69] pb-20 pt-[138px] text-white sm:pb-28 sm:pt-[165px]">
        <div class="absolute -right-36 -top-36 h-[560px] w-[560px] rounded-full border border-white/10"></div>
        <div class="absolute bottom-0 left-[-10%] h-72 w-72 rounded-full bg-[#2563eb]/30 blur-3xl"></div>
        <div class="section-wrap relative">
            <div class="max-w-2xl">
                <div
                    class="reveal inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[11px] font-semibold text-blue-100">
                    <span class="pulse-dot h-1.5 w-1.5 rounded-full bg-[#f6b51e]"></span> Platform kehadiran untuk
                    institusi Indonesia
                </div>
                <h1
                    class="reveal reveal-delay-1 mt-6 max-w-3xl text-[45px] font-extrabold leading-[1.01] tracking-[-.065em] sm:text-[70px]">
                    Setiap hadir<br><span class="text-[#f6b51e]">jadi lebih berarti.</span>
                </h1>
                <p class="reveal reveal-delay-2 mt-6 max-w-xl text-[16px] leading-7 text-blue-100/72 sm:text-[18px]">
                    Smart Absen membantu perusahaan, TPQ, sekolah, dan madrasah mencatat kehadiran dengan rapi — lalu
                    mengubahnya menjadi keputusan yang lebih cepat.
                </p>
                <div class="reveal reveal-delay-3 mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register.form') }}"
                        class="shine-button button-lift inline-flex items-center justify-center rounded-xl px-5 py-3.5 text-sm font-bold bg-[#f6b51e] text-[#172e68] hover:bg-[#ffc94e]">Coba
                        gratis 14 hari <i data-lucide="arrow-right"
                            style="width:16px;height:16px;margin-left:8px"></i></a>
                    <a href="#fitur"
                        class="button-lift inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/5 px-5 py-3.5 text-sm font-bold text-white hover:bg-white/10"><i
                            data-lucide="play" style="width:14px;height:14px;margin-right:8px;fill:currentColor"></i>
                        Lihat cara kerjanya</a>
                </div>
                <div
                    class="reveal reveal-delay-3 mt-9 flex flex-wrap gap-x-6 gap-y-3 text-[11px] font-medium text-blue-100/60">
                    <span class="flex items-center gap-1.5"><i data-lucide="shield-check"
                            style="width:14px;height:14px"></i> Data terlindungi</span>
                    <span class="flex items-center gap-1.5"><i data-lucide="zap" style="width:14px;height:14px"></i>
                        Siap dalam menit</span>
                    <span class="flex items-center gap-1.5"><i data-lucide="headphones"
                            style="width:14px;height:14px"></i> Ada pendampingan</span>
                </div>
            </div>

            <div class="reveal reveal-delay-3 relative mt-16 max-w-[1020px] text-left sm:mt-20">
                <div class="absolute -inset-4 rounded-[28px] bg-[#6ca1ff]/15 blur-2xl"></div>
                <div id="dashboard-mockup-hero"></div>
            </div>
        </div>
    </section>

    <!-- ======================= TRUST STRIP ======================= -->
    <section class="border-b border-[#e5ebf4] bg-white py-6">
        <div class="section-wrap flex flex-col items-center justify-between gap-5 sm:flex-row">
            <p class="text-center text-[12px] font-medium leading-5 text-[#8192aa] sm:text-left">Dipercaya tim yang
                ingin<br class="hidden sm:block"> lebih sedikit waktu untuk rekap.</p>
            <div
                class="flex flex-wrap justify-center gap-5 text-[12px] font-extrabold tracking-[.08em] text-[#9aabc0] sm:gap-9">
                <span>ARUNIKA</span><span>BINA NUSA</span><span>AL-HIKMAH</span><span>NUSANTARA</span>
            </div>
        </div>
    </section>

    <!-- ======================= ABOUT ======================= -->
    <section id="tentang" class="bg-white py-16 sm:py-[5.5rem]">
        <div class="section-wrap grid items-center gap-14 lg:grid-cols-[.9fr_1.1fr]">
            <div class="max-w-2xl">
                <p class="eyebrow mb-4 text-[#2563eb]">Satu sumber kebenaran</p>
                <h2 class="text-4xl font-extrabold leading-[1.06] tracking-[-.055em] sm:text-5xl text-[#102b69]">Tenang
                    untuk admin. Jelas untuk pimpinan.</h2>
                <p class="mt-5 max-w-xl text-[16px] leading-7 text-[#617590]">Smart Absen merapikan momen-momen kecil
                    yang menentukan: siapa yang hadir, siapa yang perlu ditindaklanjuti, dan bagaimana laporan sampai ke
                    orang yang tepat.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="reveal-on-scroll rounded-2xl border border-[#dfe8f3] bg-[#f3f7ff] p-6">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#dceaff] text-[#2563eb]"><i
                            data-lucide="layout-dashboard" style="width:21px;height:21px"></i></span>
                    <p class="mt-7 text-3xl font-extrabold tracking-tight text-[#102b69]">1 dashboard</p>
                    <p class="mt-2 text-sm leading-6 text-[#667c99]">untuk membaca kondisi hari ini tanpa berpindah
                        file.</p>
                </div>
                <div class="reveal-on-scroll mt-7 rounded-2xl border border-[#f1dfb7] bg-[#fffaf0] p-6 sm:mt-12">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#ffedbc] text-[#a96709]"><i
                            data-lucide="clock-3" style="width:21px;height:21px"></i></span>
                    <p class="mt-7 text-3xl font-extrabold tracking-tight text-[#102b69]">3 menit</p>
                    <p class="mt-2 text-sm leading-6 text-[#667c99]">untuk menyiapkan laporan mingguan yang rapi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================= MODULES ======================= -->
    <section id="fitur" class="bg-[#f3f7fc] py-16 sm:py-[5.5rem]">
        <div class="section-wrap">
            <div class="max-w-2xl">
                <p class="eyebrow mb-4 text-[#2563eb]">Modul yang tumbuh bersama Anda</p>
                <h2 class="text-4xl font-extrabold leading-[1.06] tracking-[-.055em] sm:text-5xl text-[#102b69]">
                    Operasional lebih ringan, dari absen sampai laporan.</h2>
                <p class="mt-5 max-w-xl text-[16px] leading-7 text-[#617590]">Aktifkan yang Anda perlukan sekarang.
                    Tambahkan modul saat struktur institusi berkembang.</p>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" id="modules-grid"></div>
        </div>
    </section>

    <!-- ======================= DIRECTORY / WEBSITE MITRA ======================= -->
    <section id="website-mitra" class="bg-white py-16 sm:py-[5.5rem]">
        <div class="section-wrap">
            <div class="flex flex-col justify-between gap-7 lg:flex-row lg:items-end">
                <div class="max-w-2xl">
                    <p class="eyebrow mb-4 text-[#2563eb]">Website mitra</p>
                    <h2 class="text-4xl font-extrabold leading-[1.06] tracking-[-.055em] sm:text-5xl text-[#102b69]">
                        Setiap lembaga punya alamat digital sendiri.</h2>
                    <p class="mt-5 max-w-xl text-[16px] leading-7 text-[#617590]">Tampilkan identitas, program, agenda,
                        dan kontak Anda dalam website publik yang terhubung langsung dengan dashboard.</p>
                </div>
                <div
                    class="flex items-center gap-2 rounded-xl border border-[#dbe5f2] bg-[#f7faff] px-4 py-3 text-xs text-[#607591]">
                    <i data-lucide="globe-2" style="width:17px;height:17px;color:#2563eb"></i> Direktori institusi aktif
                </div>
            </div>
            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm font-semibold text-[#284a7e]">Contoh ruang yang sudah berjalan</p>
                <label class="relative block sm:w-64">
                    <span class="sr-only">Cari institusi</span>
                    <input id="directory-search" class="form-control py-2.5 pl-4 pr-3 text-xs"
                        placeholder="Cari nama atau kota...">
                </label>
            </div>
            <div class="mt-5 grid gap-4 lg:grid-cols-3" id="directory-grid"></div>
        </div>
    </section>

    <!-- ======================= HOW IT WORKS ======================= -->
    <section id="cara-kerja" class="bg-[#102b69] py-16 text-white sm:py-[5.5rem]">
        <div class="section-wrap">
            <div class="max-w-2xl">
                <p class="eyebrow mb-4 text-[#9dbfff]">Mulai tanpa proyek panjang</p>
                <h2 class="text-4xl font-extrabold leading-[1.06] tracking-[-.055em] sm:text-5xl text-white">Dari kosong
                    ke tertata dalam tiga langkah.</h2>
                <p class="mt-5 max-w-xl text-[16px] leading-7 text-blue-100/70">Tidak perlu tim IT khusus. Anda tahu apa
                    yang harus dilakukan, kami bantu sisanya.</p>
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-3" id="steps-grid"></div>
        </div>
    </section>

    <!-- ======================= PRODUCT GALLERY ======================= -->
    <section class="bg-white py-16 sm:py-[5.5rem]">
        <div class="section-wrap">
            <div class="flex flex-col justify-between gap-7 lg:flex-row lg:items-end">
                <div class="max-w-2xl">
                    <p class="eyebrow mb-4 text-[#2563eb]">Produk yang mudah dipahami</p>
                    <h2 class="text-4xl font-extrabold leading-[1.06] tracking-[-.055em] sm:text-5xl text-[#102b69]">
                        Terlihat sederhana. Bekerja serius.</h2>
                    <p class="mt-5 max-w-xl text-[16px] leading-7 text-[#617590]">Dibuat untuk admin yang ingin langsung
                        mengerti, dan pengguna yang tidak ingin dipusingkan.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" id="gallery-prev"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-[#ccd9eb] text-[#3664a7] transition hover:border-[#2563eb] hover:bg-[#f0f5ff]"
                        aria-label="Produk sebelumnya"><i data-lucide="chevron-left"
                            style="width:18px;height:18px"></i></button>
                    <button type="button" id="gallery-next"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-[#ccd9eb] text-[#3664a7] transition hover:border-[#2563eb] hover:bg-[#f0f5ff]"
                        aria-label="Produk berikutnya"><i data-lucide="chevron-right"
                            style="width:18px;height:18px"></i></button>
                </div>
            </div>
            <div class="mt-10 grid items-center gap-12 lg:grid-cols-[1.2fr_.8fr]">
                <div id="gallery-visual" class="order-2 lg:order-1"></div>
                <div class="order-1 lg:order-2">
                    <div class="mb-8 flex items-center gap-4">
                        <span id="gallery-icon-wrap"
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#e7f0ff] text-[#2563eb]"><i
                                data-lucide="layout-dashboard" id="gallery-icon"
                                style="width:23px;height:23px"></i></span>
                        <div>
                            <p class="eyebrow text-[#2563eb]" id="gallery-label">Dashboard admin</p>
                            <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-[#102b69]" id="gallery-title">
                                Baca kondisi institusi dalam satu layar.</h3>
                        </div>
                    </div>
                    <p class="text-[15px] leading-7 text-[#687d98]" id="gallery-body">Ringkasan hadir, terlambat, izin,
                        dan aktivitas terbaru membantu admin bergerak tanpa menunggu rekap.</p>
                    <ul class="mt-7 space-y-4">
                        <li class="flex gap-3 text-sm font-semibold text-[#39516f]"><i data-lucide="check"
                                style="width:17px;height:17px;margin-top:2px;flex-shrink:0;color:#2563eb"></i>Tampilan
                            ringkas, hierarki informasi jelas</li>
                        <li class="flex gap-3 text-sm font-semibold text-[#39516f]"><i data-lucide="check"
                                style="width:17px;height:17px;margin-top:2px;flex-shrink:0;color:#2563eb"></i>Bisa
                            diakses dari laptop maupun ponsel</li>
                        <li class="flex gap-3 text-sm font-semibold text-[#39516f]"><i data-lucide="check"
                                style="width:17px;height:17px;margin-top:2px;flex-shrink:0;color:#2563eb"></i>Akses
                            berbasis peran dan data terenkripsi</li>
                    </ul>
                    <div class="mt-8 flex items-center gap-2 text-[11px] font-bold text-[#8395ae]"><span
                            class="h-1.5 w-1.5 rounded-full bg-[#2563eb]"></span> <span id="gallery-counter">01 /
                            02</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================= TESTIMONIALS ======================= -->
    <section class="bg-[#f3f7fc] py-16 sm:py-[5.5rem]">
        <div class="section-wrap">
            <div class="max-w-2xl">
                <p class="eyebrow mb-4 text-[#2563eb]">Cerita dari lapangan</p>
                <h2 class="text-4xl font-extrabold leading-[1.06] tracking-[-.055em] sm:text-5xl text-[#102b69]">Ketika
                    rekap beres, semua orang bernapas lebih lega.</h2>
            </div>
            <div class="mt-10 grid gap-4 lg:grid-cols-3" id="testimonials-grid"></div>
        </div>
    </section>

    <!-- ======================= PRICING ======================= -->
    <section id="harga" class="bg-white py-16 sm:py-[5.5rem]">
        <div class="section-wrap">
            <div class="mx-auto text-center max-w-2xl">
                <p class="eyebrow mb-4 text-[#2563eb]">Harga yang mengikuti langkah Anda</p>
                <h2 class="text-4xl font-extrabold leading-[1.06] tracking-[-.055em] sm:text-5xl text-[#102b69]">Mulai
                    dari yang sederhana.</h2>
                <p class="mt-5 max-w-xl mx-auto text-[16px] leading-7 text-[#617590]">Naikkan kapasitas saat institusi
                    tumbuh. Tidak ada biaya tersembunyi.</p>
            </div>
            <div class="mx-auto mt-10 grid max-w-[1080px] gap-4 lg:grid-cols-3" id="pricing-grid"></div>
        </div>
    </section>

    <!-- ======================= FAQ ======================= -->
    <section id="faq" class="bg-[#f3f7fc] py-16 sm:py-[5.5rem]">
        <div class="section-wrap grid gap-12 lg:grid-cols-[.7fr_1.3fr]">
            <div>
                <p class="eyebrow mb-4 text-[#2563eb]">Pertanyaan yang sering muncul</p>
                <h2 class="text-4xl font-extrabold leading-[1.06] tracking-[-.055em] sm:text-5xl text-[#102b69]">Supaya
                    Anda bisa mulai dengan tenang.</h2>
                <p class="mt-5 max-w-xl text-[16px] leading-7 text-[#617590]">Masih ingin melihat lebih dekat? Tim kami
                    siap menjawab kebutuhan operasional institusi Anda.</p>
                <a href="#mulai" class="mt-7 inline-flex items-center text-sm font-bold text-[#2563eb]">Tanyakan pada
                    tim kami <i data-lucide="arrow-right" style="width:15px;height:15px;margin-left:8px"></i></a>
            </div>
            <div class="divide-y divide-[#dfe7f1] rounded-2xl border border-[#dfe7f1] bg-white px-5 sm:px-7"
                id="faq-list"></div>
        </div>
    </section>

    <!-- ======================= SCHEDULE DEMO ======================= -->
    <section id="mulai" class="relative overflow-hidden bg-[#102b69] py-16 text-white sm:py-[5.5rem]">
        <div class="absolute -left-32 -top-20 h-80 w-80 rounded-full border border-white/10"></div>
        <div class="absolute -right-40 bottom-[-200px] h-[500px] w-[500px] rounded-full border border-white/10"></div>
        <div class="section-wrap relative grid gap-14 lg:grid-cols-[.9fr_1.1fr] lg:items-center">
            <div>
                <p class="eyebrow mb-4 text-[#9dbfff]">Lihat Smart Absen di institusi Anda</p>
                <h2 class="text-4xl font-extrabold leading-[1.06] tracking-[-.055em] sm:text-5xl text-white">Jadwalkan
                    demo yang relevan dengan kebutuhan Anda.</h2>
                <p class="mt-5 max-w-xl text-[16px] leading-7 text-blue-100/70">Ceritakan sedikit tentang lembaga Anda.
                    Tim kami akan menunjukkan alur yang paling dekat dengan operasional Anda.</p>
                <div class="mt-8 space-y-4 text-sm text-blue-100/75">
                    <p class="flex items-center gap-3"><i data-lucide="check"
                            style="width:17px;height:17px;color:#f6b51e"></i> Konsultasi kebutuhan tanpa biaya</p>
                    <p class="flex items-center gap-3"><i data-lucide="check"
                            style="width:17px;height:17px;color:#f6b51e"></i> Demo untuk HR, admin sekolah, atau
                        pengelola TPQ</p>
                    <p class="flex items-center gap-3"><i data-lucide="check"
                            style="width:17px;height:17px;color:#f6b51e"></i> Rekomendasi paket dan langkah mulai yang
                        jelas</p>
                </div>
            </div>

            <div
                class="rounded-2xl border border-white/12 bg-white p-6 text-[#17386f] shadow-[0_25px_70px_rgba(0,0,0,.18)] sm:p-8">
                <div id="demo-success"
                    class="hidden flex min-h-[370px] flex-col items-center justify-center text-center">
                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-[#e0f4ea] text-[#198754]"><i
                            data-lucide="check" style="width:30px;height:30px"></i></span>
                    <h3 class="mt-6 text-2xl font-extrabold tracking-tight">Permintaan demo terkirim.</h3>
                    <p class="mt-3 max-w-sm text-sm leading-6 text-[#71839b]">Terima kasih. Tim Smart Absen akan
                        menghubungi Anda untuk mengonfirmasi jadwal yang dipilih.</p>
                    <button type="button" id="demo-reset" class="mt-7 text-sm font-bold text-[#2563eb]">Kirim permintaan
                        lain</button>
                </div>

                <form id="demo-form" class="space-y-4" novalidate>
                    <div class="flex items-center justify-between border-b border-[#e8eef6] pb-4">
                        <div>
                            <p class="eyebrow text-[#2563eb]">Demo pribadi</p>
                            <h3 class="mt-1 text-xl font-extrabold">Pilih waktu yang nyaman.</h3>
                        </div>
                        <i data-lucide="calendar-days" style="width:25px;height:25px;color:#2563eb"></i>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block sm:col-span-2"><span
                                class="mb-1.5 block text-xs font-bold text-[#456282]">Nama Lengkap</span><input required
                                name="name" class="form-control" placeholder="Contoh: Nadia Anindita"></label>
                        <label class="block"><span class="mb-1.5 block text-xs font-bold text-[#456282]">Nomor
                                WhatsApp</span><input required name="phone" type="tel" class="form-control"
                                placeholder="08xxxxxxxxxx"></label>
                        <label class="block"><span class="mb-1.5 block text-xs font-bold text-[#456282]">Nama
                                Lembaga/Perusahaan</span><input required name="institution" class="form-control"
                                placeholder="Nama institusi"></label>
                        <label class="block sm:col-span-2"><span
                                class="mb-1.5 block text-xs font-bold text-[#456282]">Alamat</span><input required
                                name="address" class="form-control" placeholder="Kota atau alamat institusi"></label>
                        <label class="block"><span
                                class="mb-1.5 block text-xs font-bold text-[#456282]">Tanggal</span><input required
                                name="date" type="date" class="form-control"></label>
                        <label class="block"><span
                                class="mb-1.5 block text-xs font-bold text-[#456282]">Jam</span><input required
                                name="time" type="time" class="form-control"></label>
                    </div>
                    <p id="demo-error"
                        class="hidden rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">Lengkapi
                        semua kolom agar kami bisa menyiapkan demo.</p>
                    <button type="submit"
                        class="shine-button button-lift mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-[#f6b51e] py-3.5 text-sm font-extrabold text-[#172e68] hover:bg-[#ffc94e]">Jadwalkan
                        demo <i data-lucide="arrow-right" style="width:16px;height:16px"></i></button>
                    <p class="text-center text-[10px] text-[#8a9ab0]">Tanpa komitmen. Data Anda hanya digunakan untuk
                        mengatur demo.</p>
                </form>
            </div>
        </div>
    </section>

    <!-- ======================= FOOTER ======================= -->
    <footer class="bg-[#0b2257] py-12 text-blue-100/65">
        <div class="section-wrap">
            <div class="flex flex-col justify-between gap-8 border-b border-white/10 pb-10 sm:flex-row">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f6b51e] text-[#122e70]"><i
                                data-lucide="calendar-check-2"
                                style="width:19px;height:19px;stroke-width:2.5"></i></span>
                        <span class="text-[17px] font-extrabold tracking-[-.06em] text-white">smart<span
                                class="text-[#f0a914]">absen</span></span>
                    </div>
                    <p class="mt-4 max-w-xs text-sm leading-6">Lapisan kehadiran yang tenang, untuk keputusan yang lebih
                        jelas.</p>
                </div>
                <div class="grid grid-cols-2 gap-x-12 gap-y-7 text-sm sm:grid-cols-3">
                    <div>
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-white/45">Produk</p>
                        <a href="#fitur" class="block py-1 hover:text-white">Fitur</a>
                        <a href="#harga" class="block py-1 hover:text-white">Harga</a>
                        <a href="#faq" class="block py-1 hover:text-white">FAQ</a>
                    </div>
                    <div>
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-white/45">Institusi</p>
                        <a href="#website-mitra" class="block py-1 hover:text-white">Website Mitra</a>
                        <a href="mailto:halo@smartabsen.id" class="block py-1 hover:text-white">Kontak</a>
                    </div>
                    <div>
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-white/45">Akses</p>
                        <a href="{{ route('login.form') }}" class="block py-1 hover:text-white">Masuk dashboard</a>
                        <a href="{{ route('register.form') }}" class="block py-1 hover:text-white">Daftar institusi</a>
                    </div>
                </div>
            </div>
            <div class="flex flex-col justify-between gap-3 pt-7 text-[11px] sm:flex-row">
                <p>© 2026 Smart Absen · Dibuat untuk institusi Indonesia.</p>
                <div class="flex gap-5"><span>Privasi</span><span>Ketentuan</span></div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>