// ==========================================================================
// Smart Absen — Landing Page (vanilla JS)
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();
  initMobileMenu();
  initActiveNav();
  initRevealOnScroll();
  renderDashboardMockup('dashboard-mockup-hero');
  renderModules();
  renderDirectory();
  renderSteps();
  initProductGallery();
  renderTestimonials();
  renderPricing();
  renderFAQ();
  initDemoForm();
  lucide.createIcons(); // re-run after dynamic content injected
});

// ---------------------------------------------------------------------
// Mobile menu
// ---------------------------------------------------------------------
function initMobileMenu() {
  const btn = document.getElementById('mobile-menu-btn');
  const menu = document.getElementById('mobile-menu');
  const iconOpen = document.getElementById('icon-menu-open');
  const iconClose = document.getElementById('icon-menu-close');
  let open = false;

  btn.addEventListener('click', () => {
    open = !open;
    menu.classList.toggle('hidden', !open);
    iconOpen.style.display = open ? 'none' : 'block';
    iconClose.style.display = open ? 'block' : 'none';
  });

  document.querySelectorAll('.mobile-nav-link, #mobile-menu a').forEach((link) => {
    link.addEventListener('click', () => {
      open = false;
      menu.classList.add('hidden');
      iconOpen.style.display = 'block';
      iconClose.style.display = 'none';
    });
  });
}

// ---------------------------------------------------------------------
// Active nav highlight on scroll
// ---------------------------------------------------------------------
function initActiveNav() {
  const navLinks = document.querySelectorAll('#desktop-nav a, .mobile-nav-link');
  const sections = Array.from(navLinks)
    .map((a) => document.querySelector(a.getAttribute('href')))
    .filter(Boolean);

  const setActive = (label) => {
    navLinks.forEach((a) => {
      const isActive = a.dataset.nav === label;
      a.classList.toggle('nav-active', isActive);
      if (!a.classList.contains('mobile-nav-link')) {
        a.classList.toggle('text-[#607591]', !isActive);
      } else {
        a.classList.toggle('text-[#526a88]', !isActive);
        a.classList.toggle('text-[#2563eb]', isActive);
      }
    });
  };

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const id = entry.target.id;
          const link = document.querySelector(`#desktop-nav a[href="#${id}"]`);
          if (link) setActive(link.dataset.nav);
        }
      });
    },
    { rootMargin: '-25% 0px -65% 0px' }
  );
  sections.forEach((s) => observer.observe(s));
}

// ---------------------------------------------------------------------
// Reveal on scroll
// ---------------------------------------------------------------------
function initRevealOnScroll() {
  const items = document.querySelectorAll('.reveal-on-scroll');
  const observer = new IntersectionObserver(
    (entries) => entries.forEach((entry) => { if (entry.isIntersecting) entry.target.classList.add('is-visible'); }),
    { threshold: 0.12 }
  );
  items.forEach((item) => observer.observe(item));
}

// ---------------------------------------------------------------------
// Dashboard mockup (used in hero + product gallery)
// ---------------------------------------------------------------------
function browserFrame(innerHtml, title = 'app.smartabsen.id') {
  return `
  <div class="overflow-hidden rounded-[20px] border border-[#cbd9eb] bg-white shadow-[0_30px_75px_rgba(17,57,126,.16)]">
    <div class="flex h-10 items-center gap-2 border-b border-[#e7edf5] bg-[#f8fafd] px-4">
      <span class="h-2 w-2 rounded-full bg-[#f3a8a8]"></span>
      <span class="h-2 w-2 rounded-full bg-[#f0cc7e]"></span>
      <span class="h-2 w-2 rounded-full bg-[#91d3ac]"></span>
      <div class="ml-3 flex h-6 flex-1 items-center rounded-md bg-white px-3 text-[9px] text-[#91a4bf] shadow-sm">
        <i data-lucide="globe-2" style="width:10px;height:10px;margin-right:8px;color:#6d9be8"></i>${title}
      </div>
    </div>
    ${innerHtml}
  </div>`;
}

function dashboardMockupHTML() {
  const stats = [
    ['Hadir', '128', '#e0f4ea', '#198754'],
    ['Terlambat', '12', '#fff1dc', '#bd690b'],
    ['Izin', '06', '#e2ecff', '#2563eb'],
    ['Belum absen', '09', '#fbe5e5', '#c24141'],
  ];
  const bars = [61, 77, 68, 86, 74, 91, 80];
  const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
  const activity = ['Sarah Amalia', 'Fajar Nugroho', 'Nadia Anindita'];

  const inner = `
  <div class="flex min-h-[390px] bg-[#f5f8fc] text-left">
    <aside class="hidden w-[145px] shrink-0 bg-[#102b69] p-3 sm:block">
      <div class="mb-8 flex items-center gap-1.5 text-[10px] font-bold text-white">
        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-[#f6b51e] text-[#102b69]"><i data-lucide="calendar-check-2" style="width:12px;height:12px"></i></span> Smart Absen
      </div>
      <div class="space-y-1 text-[9px]">
        <p class="flex items-center gap-2 rounded-lg bg-white/15 px-2 py-2 font-semibold text-white"><i data-lucide="layout-dashboard" style="width:12px;height:12px"></i> Ringkasan</p>
        <p class="flex items-center gap-2 px-2 py-2 text-blue-100/60"><i data-lucide="users" style="width:12px;height:12px"></i> Anggota</p>
        <p class="flex items-center gap-2 px-2 py-2 text-blue-100/60"><i data-lucide="calendar-check-2" style="width:12px;height:12px"></i> Kehadiran</p>
        <p class="flex items-center gap-2 px-2 py-2 text-blue-100/60"><i data-lucide="clipboard-check" style="width:12px;height:12px"></i> Pengajuan</p>
        <p class="flex items-center gap-2 px-2 py-2 text-blue-100/60"><i data-lucide="file-bar-chart-2" style="width:12px;height:12px"></i> Laporan</p>
      </div>
      <div class="mt-16 rounded-lg border border-white/10 bg-white/5 p-2 text-[8px] text-blue-100/65">Paket Pro<br><b class="text-white">Aktif hingga 21 Sep 2026</b></div>
    </aside>
    <div class="min-w-0 flex-1 p-4 sm:p-5">
      <div class="mb-5 flex items-start justify-between">
        <div>
          <p class="text-[9px] text-[#8092ad]">Senin, 15 September 2026</p>
          <p class="mt-1 text-[17px] font-bold text-[#183568]">Selamat pagi, Nadia</p>
        </div>
        <div class="flex items-center gap-3">
          <i data-lucide="bell" style="width:14px;height:14px;color:#8397b4"></i>
          <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#f5c88a] text-[8px] font-bold text-[#855014]">NA</span>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4">
        ${stats.map(([label, number, bg, color]) => `
          <div class="rounded-lg border border-[#e7edf5] border-t-2 bg-white p-2.5" style="border-top-color:${color}">
            <p class="text-[8px] text-[#8b9ab0]">${label}</p>
            <p class="mt-1 text-lg font-bold" style="color:${color}">${number}</p>
            <div class="mt-1 h-1 rounded-full" style="background-color:${bg}"><div class="h-1 w-3/4 rounded-full" style="background-color:${color}"></div></div>
          </div>`).join('')}
      </div>
      <div class="mt-3 grid gap-3 sm:grid-cols-[1.35fr_1fr]">
        <div class="rounded-lg border border-[#e7edf5] bg-white p-3">
          <div class="flex items-center justify-between">
            <p class="text-[10px] font-bold text-[#284572]">Tren kehadiran minggu ini</p>
            <span class="text-[8px] text-[#8b9ab0]">15–19 Sep</span>
          </div>
          <div class="mt-5 flex h-[93px] items-end justify-between gap-2 px-1">
            ${bars.map((h, i) => `
              <div class="flex h-full flex-1 flex-col justify-end gap-1">
                <div class="rounded-t-sm bg-[#76a8fa]" style="height:${h}%"></div>
                <span class="text-center text-[7px] text-[#a0aec0]">${days[i]}</span>
              </div>`).join('')}
          </div>
        </div>
        <div class="rounded-lg border border-[#e7edf5] bg-white p-3">
          <p class="text-[10px] font-bold text-[#284572]">Aktivitas terakhir</p>
          <div class="mt-4 space-y-3">
            ${activity.map((name, i) => `
              <div class="flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#e0ebff] text-[7px] font-bold text-[#2c5fb8]">${name.split(' ').map((x) => x[0]).join('')}</span>
                <div class="min-w-0 flex-1">
                  <p class="truncate text-[8px] font-semibold text-[#435a7b]">${name}</p>
                  <p class="text-[7px] text-[#99a8ba]">Presensi masuk</p>
                </div>
                <span class="text-[8px] font-semibold text-[#198754]">07.5${i}</span>
              </div>`).join('')}
          </div>
        </div>
      </div>
    </div>
  </div>`;
  return browserFrame(inner);
}

function phoneMockupHTML() {
  return `
  <div class="mx-auto w-[220px] rounded-[30px] border-[7px] border-[#172e68] bg-[#f5f8fc] p-2 shadow-[0_22px_40px_rgba(15,48,115,.23)]">
    <div class="mx-auto mb-3 h-1 w-16 rounded-full bg-[#c3cee2]"></div>
    <div class="rounded-[21px] bg-[#f5f8fc] p-3">
      <div class="flex items-center justify-between text-[#183568]">
        <div><p class="text-[8px] text-[#8193ac]">Selamat pagi</p><p class="text-[12px] font-bold">Nadia Anindita</p></div>
        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#f5c88a] text-[9px] font-bold text-[#855014]">NA</span>
      </div>
      <div class="mt-4 rounded-[15px] bg-[#102b69] p-3 text-white">
        <div class="flex items-center justify-between">
          <span class="text-[8px] text-blue-100">Senin, 15 September</span>
          <i data-lucide="mouse-pointer-2" style="width:13px;height:13px;color:#f5b51e"></i>
        </div>
        <p class="mt-3 text-[22px] font-extrabold tracking-tight">07:58</p>
        <p class="text-[8px] text-blue-200/75">Kantor Pusat · Dalam radius</p>
        <button type="button" class="mt-4 flex w-full items-center justify-center gap-1.5 rounded-lg bg-[#f6b51e] py-2 text-[9px] font-bold text-[#172e68]"><i data-lucide="scan-face" style="width:12px;height:12px"></i> Absen masuk</button>
      </div>
      <p class="mb-2 mt-5 text-[10px] font-bold text-[#284572]">Aktivitas hari ini</p>
      <div class="space-y-2">
        <div class="flex items-center justify-between rounded-lg border border-[#e5ebf4] bg-white p-2"><span class="text-[9px] font-medium text-[#536985]">Absen masuk</span><span class="text-[9px] font-bold text-[#198754]">07:58</span></div>
        <div class="flex items-center justify-between rounded-lg border border-[#e5ebf4] bg-white p-2"><span class="text-[9px] font-medium text-[#536985]">Pulang</span><span class="text-[9px] text-[#96a6ba]">—</span></div>
      </div>
      <div class="mt-5 flex items-center gap-2 rounded-lg bg-[#e7f0ff] p-2 text-[8px] leading-3 text-[#3562aa]"><i data-lucide="shield-check" style="width:14px;height:14px"></i><span>Lokasi dan selfie terenkripsi.</span></div>
    </div>
  </div>`;
}

function renderDashboardMockup(targetId) {
  document.getElementById(targetId).innerHTML = dashboardMockupHTML();
}

// ---------------------------------------------------------------------
// Modules
// ---------------------------------------------------------------------
const MODULES = [
  { icon: 'wallet-cards', title: 'HR & Payroll', desc: 'Data presensi siap menjadi dasar payroll, lembur, dan evaluasi tim.', tone: 'blue' },
  { icon: 'scan-face', title: 'Kehadiran', desc: 'Selfie, GPS, shift, dan ringkasan real-time tanpa titip absen.', tone: 'blue' },
  { icon: 'clipboard-check', title: 'Izin & Persetujuan', desc: 'Pengajuan mengalir ke approver yang tepat dengan jejak yang jelas.', tone: 'amber' },
  { icon: 'file-bar-chart-2', title: 'Laporan', desc: 'Filter per unit, kelas, lokasi, atau tanggal. Unduh saat dibutuhkan.', tone: 'blue' },
];

function renderModules() {
  const grid = document.getElementById('modules-grid');
  grid.innerHTML = MODULES.map((m, i) => `
    <article class="card-lift reveal-on-scroll rounded-2xl border border-[#dfe8f3] bg-white p-6">
      <span class="flex h-12 w-12 items-center justify-center rounded-xl ${m.tone === 'amber' ? 'bg-[#fff0c9] text-[#ac6907]' : 'bg-[#e0ebff] text-[#2563eb]'}">
        <i data-lucide="${m.icon}" style="width:22px;height:22px"></i>
      </span>
      <p class="mt-7 text-[11px] font-bold uppercase tracking-[.14em] text-[#8092aa]">0${i + 1}</p>
      <h3 class="mt-3 text-xl font-extrabold tracking-tight text-[#15366f]">${m.title}</h3>
      <p class="mt-3 text-sm leading-6 text-[#697f9b]">${m.desc}</p>
      <a href="#mulai" class="mt-7 inline-flex items-center text-xs font-bold text-[#2563eb]">Lihat detail <i data-lucide="arrow-up-right" style="width:13px;height:13px;margin-left:4px"></i></a>
    </article>`).join('');
  lucide.createIcons();
}

// ---------------------------------------------------------------------
// Directory / Website Mitra
// ---------------------------------------------------------------------
// Data contoh, dipakai HANYA kalau belum ada tenant aktif di database.
const DIRECTORY_FALLBACK = [
  { subdomain: 'tpq.ptutamacta.com', name: 'TPQ Al-Hikmah', type: 'TPQ & Madrasah', city: 'Bandung', initials: 'AH', tone: 'bg-tone-amber' },
  { subdomain: 'sekolah-cendekia.ptutamacta.com', name: 'Sekolah Cendekia', type: 'Sekolah', city: 'Surabaya', initials: 'SC', tone: 'bg-tone-blue' },
  { subdomain: 'perusahaan-nusantara.ptutamacta.com', name: 'Perusahaan Nusantara', type: 'Perusahaan', city: 'Jakarta', initials: 'PN', tone: 'bg-tone-green' },
];

// window.TENANT_DIRECTORY diisi dari Blade (data tenant asli dari database).
const DIRECTORY_ITEMS = (window.TENANT_DIRECTORY && window.TENANT_DIRECTORY.length)
  ? window.TENANT_DIRECTORY
  : DIRECTORY_FALLBACK;

function renderDirectory(query = '') {
  const grid = document.getElementById('directory-grid');
  const filtered = DIRECTORY_ITEMS.filter((item) =>
    `${item.name} ${item.type} ${item.city}`.toLowerCase().includes(query.toLowerCase())
  );

  if (filtered.length === 0) {
    grid.innerHTML = `<div class="rounded-2xl border border-dashed border-[#b9cce8] bg-[#f8fafd] p-10 text-center text-sm text-[#71839b] lg:col-span-3">Belum ada institusi yang cocok. Coba kata kunci lain.</div>`;
    return;
  }

  grid.innerHTML = filtered.map((item) => `
    <article class="directory-card rounded-2xl border border-[#dfe8f3] bg-[#f8fafd] p-5">
      <div class="flex items-start justify-between">
        <span class="flex h-11 w-11 items-center justify-center rounded-xl text-xs font-extrabold ${item.tone}">${item.initials}</span>
        <span class="rounded-full bg-[#e4f6ed] px-2 py-1 text-[9px] font-bold text-[#188057]">AKTIF</span>
      </div>
      <h3 class="mt-6 text-lg font-extrabold text-[#15366f]">${item.name}</h3>
      <p class="mt-1 text-xs text-[#7a8da7]">${item.type}${item.city ? ' · ' + item.city : ''}</p>
      <div class="mt-5 flex items-center gap-2 rounded-lg border border-[#e0e8f2] bg-white px-3 py-2.5 text-[10px] font-semibold text-[#4e6b93]">
        <i data-lucide="link-2" style="width:13px;height:13px;color:#2563eb"></i>${item.subdomain}
      </div>
      <a href="${window.location.protocol}//${item.subdomain}${window.location.port ? ':' + window.location.port : ''}" class="button-lift mt-4 flex w-full items-center justify-center gap-2 rounded-lg border border-[#c7d7eb] bg-white py-2.5 text-xs font-bold text-[#2456b5] hover:border-[#2563eb]">
        Kunjungi website <i data-lucide="arrow-up-right" style="width:14px;height:14px"></i>
      </a>
    </article>`).join('');
  lucide.createIcons();
}

document.addEventListener('input', (e) => {
  if (e.target && e.target.id === 'directory-search') {
    renderDirectory(e.target.value);
  }
});

// ---------------------------------------------------------------------
// Steps ("Cara Kerja")
// ---------------------------------------------------------------------
const STEPS = [
  ['01', 'building-2', 'Daftarkan institusi', 'Isi nama lembaga dan pilih struktur yang sesuai kebutuhan Anda.'],
  ['02', 'sparkles', 'Atur ruang kerja', 'Tambah anggota, lokasi, kelas, atau divisi. Sesuaikan aturan presensi.'],
  ['03', 'bar-chart-3', 'Ambil keputusan', 'Buka dashboard, kirim notifikasi, dan unduh laporan saat dibutuhkan.'],
];

function renderSteps() {
  const grid = document.getElementById('steps-grid');
  grid.innerHTML = STEPS.map(([num, icon, title, body], i) => `
    <div class="relative rounded-2xl border border-white/12 bg-white/[.06] p-7 backdrop-blur-sm">
      <div class="flex items-start justify-between">
        <span class="text-4xl font-extrabold tracking-tight text-white/15">${num}</span>
        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#2563eb] text-white"><i data-lucide="${icon}" style="width:21px;height:21px"></i></span>
      </div>
      <h3 class="mt-12 text-xl font-extrabold">${title}</h3>
      <p class="mt-3 text-sm leading-6 text-blue-100/65">${body}</p>
      ${i < 2 ? '<i data-lucide="arrow-right" class="absolute -right-4 top-1/2 z-10 hidden md:block" style="color:#f6b51e;width:21px;height:21px"></i>' : ''}
    </div>`).join('');
  lucide.createIcons();
}

// ---------------------------------------------------------------------
// Product gallery (dashboard / phone slider)
// ---------------------------------------------------------------------
function initProductGallery() {
  const slides = [
    { label: 'Dashboard admin', title: 'Baca kondisi institusi dalam satu layar.', body: 'Ringkasan hadir, terlambat, izin, dan aktivitas terbaru membantu admin bergerak tanpa menunggu rekap.', icon: 'layout-dashboard', visual: dashboardMockupHTML, wrapClass: '' },
    { label: 'Aplikasi pengguna', title: 'Presensi yang terasa sederhana.', body: 'Anggota cukup membuka aplikasi, melakukan selfie, dan sistem memvalidasi lokasi secara otomatis.', icon: 'smartphone', visual: phoneMockupHTML, wrapClass: 'flex min-h-[430px] items-center justify-center rounded-[24px] bg-[#edf4ff]' },
  ];
  let slide = 0;

  function render() {
    const current = slides[slide];
    document.getElementById('gallery-visual').className = `order-2 lg:order-1 ${current.wrapClass}`;
    document.getElementById('gallery-visual').innerHTML = current.visual();
    document.getElementById('gallery-icon').setAttribute('data-lucide', current.icon);
    document.getElementById('gallery-label').textContent = current.label;
    document.getElementById('gallery-title').textContent = current.title;
    document.getElementById('gallery-body').textContent = current.body;
    document.getElementById('gallery-counter').textContent = `0${slide + 1} / 02`;
    lucide.createIcons();
  }

  document.getElementById('gallery-prev').addEventListener('click', () => {
    slide = (slide + slides.length - 1) % slides.length;
    render();
  });
  document.getElementById('gallery-next').addEventListener('click', () => {
    slide = (slide + 1) % slides.length;
    render();
  });

  render();
}

// ---------------------------------------------------------------------
// Testimonials
// ---------------------------------------------------------------------
const TESTIMONIALS = [
  ['“Sebelumnya kami merekap dari tiga file berbeda. Sekarang kepala cabang bisa melihat kondisi tim sebelum rapat pagi dimulai.”', 'Rizky Fadillah', 'HR Manager, Arunika Logistik', 'RF', false],
  ['“Wali santri tidak lagi bertanya satu per satu. Notifikasi berjalan, guru tinggal mengajar.”', 'Ustazah Nabila Karim', 'Koordinator TPQ Al-Hikmah, Bandung', 'NK', true],
  ['“Laporan bulanan yang dulu selesai dua hari, sekarang bisa kami kirim ke yayasan dalam hitungan menit.”', 'Dewi Lestari', 'Kepala Tata Usaha, SMP Bina Nusa', 'DL', false],
];

function renderTestimonials() {
  const grid = document.getElementById('testimonials-grid');
  grid.innerHTML = TESTIMONIALS.map(([quote, name, role, initials, isAmber]) => `
    <blockquote class="card-lift reveal-on-scroll flex flex-col rounded-2xl border border-[#dfe8f3] bg-white p-7">
      <i data-lucide="message-circle" style="width:22px;height:22px;color:#9db7e5"></i>
      <p class="mt-6 flex-1 text-[15px] leading-7 text-[#3f5878]">${quote}</p>
      <footer class="mt-8 flex items-center gap-3 border-t border-[#edf1f6] pt-5">
        <span class="flex h-10 w-10 items-center justify-center rounded-full text-xs font-extrabold ${isAmber ? 'bg-[#fff0d9] text-[#ad5b09]' : 'bg-[#dceaff] text-[#1e40af]'}">${initials}</span>
        <div><p class="text-sm font-bold text-[#17386f]">${name}</p><p class="mt-0.5 text-[11px] text-[#8192aa]">${role}</p></div>
      </footer>
    </blockquote>`).join('');
  lucide.createIcons();
}

// ---------------------------------------------------------------------
// Pricing
// ---------------------------------------------------------------------
const PLANS = [
  { name: 'Mulai', desc: 'Untuk tim kecil yang ingin mulai tertata.', price: 'Gratis', note: '14 hari pertama', features: ['Sampai 25 anggota', 'Presensi selfie + GPS', 'Dashboard ringkasan', 'Ekspor Excel'], cta: 'Mulai gratis' },
  { name: 'Berkembang', desc: 'Untuk institusi yang siap bekerja lebih rapi.', price: 'Rp 149k', note: 'per bulan / institusi', features: ['Sampai 150 anggota', 'Semua fitur Mulai', 'Izin, cuti, & notifikasi', 'Website publik + subdomain', 'Laporan PDF & payroll'], cta: 'Pilih Berkembang' },
  { name: 'Menyeluruh', desc: 'Untuk kebutuhan multi-unit dan skala lebih besar.', price: 'Diskusi', note: 'sesuai kebutuhan', features: ['Anggota tanpa batas', 'Multi lokasi & struktur', 'Akses peran lanjutan', 'Pendampingan implementasi', 'Dukungan prioritas'], cta: 'Bicarakan kebutuhan' },
];

function renderPricing() {
  const grid = document.getElementById('pricing-grid');
  grid.innerHTML = PLANS.map((plan, i) => {
    const highlighted = i === 1;
    return `
    <div class="relative flex flex-col rounded-2xl border p-7 ${highlighted ? 'border-[#2563eb] bg-[#102b69] text-white shadow-[0_20px_50px_rgba(16,43,105,.2)]' : 'border-[#dfe8f3] bg-[#f8fafd]'}">
      ${highlighted ? '<span class="absolute -top-3 right-6 rounded-full bg-[#f6b51e] px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider text-[#172e68]">Paling dipilih</span>' : ''}
      <p class="eyebrow ${highlighted ? 'text-[#9dbfff]' : 'text-[#2563eb]'}">${plan.name}</p>
      <p class="mt-3 min-h-[48px] text-sm leading-6 ${highlighted ? 'text-blue-100/65' : 'text-[#687d98]'}">${plan.desc}</p>
      <p class="mt-7 text-3xl font-extrabold tracking-tight">${plan.price}</p>
      <p class="mt-1 text-[11px] ${highlighted ? 'text-blue-100/55' : 'text-[#8192aa]'}">${plan.note}</p>
      <div class="my-7 border-t ${highlighted ? 'border-white/15' : 'border-[#dfe7f1]'}"></div>
      <ul class="flex-1 space-y-3">
        ${plan.features.map((f) => `<li class="flex items-center gap-2 text-sm ${highlighted ? 'text-blue-50/80' : 'text-[#506783]'}"><i data-lucide="check" style="width:15px;height:15px;color:${highlighted ? '#f6b51e' : '#2563eb'}"></i>${f}</li>`).join('')}
      </ul>
      <a href="/login" class="button-lift mt-8 rounded-xl px-4 py-3 text-center text-sm font-bold ${highlighted ? 'bg-[#f6b51e] text-[#172e68]' : 'border border-[#cbd9ec] bg-white text-[#1e40af]'}">${plan.cta} <i data-lucide="arrow-right" style="width:14px;height:14px;margin-left:4px"></i></a>
    </div>`;
  }).join('');
  lucide.createIcons();
}

// ---------------------------------------------------------------------
// FAQ
// ---------------------------------------------------------------------
const FAQ_ITEMS = [
  ['Apakah Smart Absen cocok untuk perusahaan dan sekolah sekaligus?', 'Bisa. Struktur Smart Absen dapat mengikuti divisi, cabang, kelas, rombel, atau unit yayasan. Satu platform untuk berbagai ritme kerja.'],
  ['Bagaimana cara kerja selfie dan GPS saat presensi?', 'Pengguna melakukan presensi dari perangkat masing-masing. Sistem mencatat selfie, titik GPS, waktu, dan aturan radius yang Anda tentukan.'],
  ['Apakah setiap lembaga mendapat website publik sendiri?', 'Ya. Saat mendaftar, Anda menerima subdomain unik dan dapat mengatur logo, warna, program, agenda, serta kontak lembaga.'],
  ['Bisakah wali santri menerima kabar kehadiran?', 'Bisa. Modul notifikasi dapat mengirim pembaruan hadir, izin, atau tidak hadir kepada orang tua sesuai aturan lembaga.'],
  ['Apakah laporan dapat diolah untuk payroll?', 'Rekap dapat difilter per orang, unit, lokasi, dan periode lalu diunduh sebagai Excel atau PDF untuk proses payroll dan pelaporan.'],
  ['Berapa lama lembaga bisa mulai menggunakan Smart Absen?', 'Ruang kerja siap dibuat dalam hitungan menit. Setelah itu admin tinggal menambahkan anggota, lokasi, dan aturan presensi yang dibutuhkan.'],
];

function renderFAQ() {
  const list = document.getElementById('faq-list');
  list.innerHTML = FAQ_ITEMS.map(([q, a], i) => `
    <div>
      <button type="button" class="faq-toggle flex w-full items-center justify-between gap-5 py-5 text-left text-sm font-bold text-[#274570]" data-index="${i}">
        <span>${q}</span>
        <i data-lucide="chevron-down" class="faq-chevron shrink-0" style="width:17px;height:17px;color:#7190be;transition:transform .2s"></i>
      </button>
      <div class="faq-answer hidden pb-5 pr-8 text-[13px] leading-6 text-[#70839d]">${a}</div>
    </div>`).join('');
  lucide.createIcons();

  list.querySelectorAll('.faq-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const answer = btn.nextElementSibling;
      const chevron = btn.querySelector('.faq-chevron');
      const isOpen = !answer.classList.contains('hidden');

      // close all
      list.querySelectorAll('.faq-answer').forEach((el) => el.classList.add('hidden'));
      list.querySelectorAll('.faq-chevron').forEach((el) => el.style.transform = 'rotate(0deg)');

      if (!isOpen) {
        answer.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
      }
    });
  });

  // open first item by default
  const first = list.querySelector('.faq-toggle');
  if (first) first.click();
}

// ---------------------------------------------------------------------
// Schedule demo form
// ---------------------------------------------------------------------
function initDemoForm() {
  const form = document.getElementById('demo-form');
  const success = document.getElementById('demo-success');
  const errorEl = document.getElementById('demo-error');
  const resetBtn = document.getElementById('demo-reset');

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form.checkValidity()) {
      errorEl.classList.remove('hidden');
      return;
    }
    errorEl.classList.add('hidden');
    form.classList.add('hidden');
    success.classList.remove('hidden');
    success.classList.add('flex');
    lucide.createIcons();
  });

  resetBtn.addEventListener('click', () => {
    success.classList.add('hidden');
    success.classList.remove('flex');
    form.classList.remove('hidden');
    form.reset();
  });
}
