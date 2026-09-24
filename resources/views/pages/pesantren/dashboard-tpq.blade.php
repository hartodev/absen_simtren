{{-- Beranda HIJAU: dashboard TPQ (ustadz) --}}
@extends('layouts.admin')
@section('title', 'Beranda')
@section('subtitle', $company->name)
@section('width', 'max-w-7xl')

@section('actions')
    <a href="{{ route($rp.'attendances.index') }}" class="btn btn-line">
        <span class="h-2 w-2 rounded-full {{ $attToday?->time_in ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
        {{ $attToday?->time_in ? 'Sudah absen '.\Illuminate\Support\Str::substr($attToday->time_in, 0, 5) : 'Belum absen' }}
    </a>
@endsection

@section('content')
@php
    $statCards = [
        ['Hadir', $att['hadir'], 'text-emerald-700', 'bg-emerald-50', 'check-circle-2'],
        ['Telat', $att['telat'], 'text-amber-600',   'bg-amber-50',   'clock'],
        ['Absen', $att['absen'], 'text-red-700',     'bg-red-50',     'x-circle'],
        ['Izin',  $att['izin'],  'text-slate-600',   'bg-slate-100',  'file-text'],
    ];
@endphp

{{-- Sapaan --}}
<div class="card mb-6 flex items-center gap-4 p-5" style="background: var(--brand); border-color: transparent; color: #fff">
    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full border-2 border-white/40 bg-white/10 text-lg font-semibold">
        {{ strtoupper(mb_substr($me->name, 0, 2)) }}
    </span>
    <div class="min-w-0">
        <p class="text-sm text-white/80">Assalamu'alaikum,</p>
        <h2 class="truncate text-xl font-bold">{{ $me->name }}</h2>
        <p class="text-xs text-white/70">Kehadiran Anda bulan {{ $monthLabel }}</p>
    </div>
</div>

{{-- Statistik kehadiran ustadz --}}
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    @foreach ($statCards as [$label, $val, $cls, $bg, $icon])
        <div class="card flex items-center gap-4 p-4">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $bg }} {{ $cls }}"><i data-lucide="{{ $icon }}" class="h-5 w-5"></i></span>
            <div><p class="text-2xl font-bold {{ $cls }}">{{ $val }}</p><p class="text-xs text-slate-500">{{ $label }}</p></div>
        </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        {{-- Jadwal sholat (dimuat di browser dari api.aladhan.com, metode Kemenag RI) --}}
        <section>
            <div class="mb-2 flex items-baseline justify-between">
                <h2 class="font-bold text-slate-800">Jadwal sholat</h2>
                <p class="truncate pl-4 text-xs text-slate-400">{{ $company->address ?: 'Lokasi lembaga' }}</p>
            </div>
            <div id="prayer" class="card p-4">
                <p id="prayer-msg" class="px-1 py-3 text-sm text-slate-400">Memuat jadwal sholat…</p>
            </div>
        </section>

        <section>
            <div class="mb-2 flex items-center justify-between">
                <h2 class="font-bold text-slate-800">Mutaba'ah hari ini</h2>
                <a href="{{ route($rp.'mutabaah.index') }}" class="text-sm font-semibold" style="color: var(--brand)">Lihat rekap →</a>
            </div>
            <div class="card p-5">
                <div class="grid grid-cols-3 divide-x divide-slate-100 text-center">
                    <div><p class="text-3xl font-bold" style="color: var(--brand)">{{ $mutabaah['total'] }}</p><p class="text-xs text-slate-500">Total sesi</p></div>
                    <div><p class="text-3xl font-bold text-amber-600">{{ $mutabaah['belum'] }}</p><p class="text-xs text-slate-500">Belum paraf</p></div>
                    <div><p class="text-3xl font-bold" style="color: var(--brand)">{{ $mutabaah['sudah'] }}</p><p class="text-xs text-slate-500">Sudah paraf</p></div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full" style="width: {{ $mutabaah['persen'] }}%; background: var(--brand)"></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">{{ $mutabaah['persen'] }}%</span>
                </div>
            </div>
        </section>

        <section>
            <h2 class="mb-2 font-bold text-slate-800">Ringkasan {{ $monthLabel }}</h2>
            <div class="grid grid-cols-3 gap-4">
                @foreach ([['Total santri', $summary['santri']], ['Total hadir', $summary['hadir']], ["Sesi mutaba'ah", $summary['sesi']]] as [$l, $v])
                    <div class="card py-5 text-center">
                        <p class="text-2xl font-bold" style="color: var(--brand)">{{ $v }}</p>
                        <p class="text-xs text-slate-500">{{ $l }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section>
            <h2 class="mb-2 font-bold text-slate-800">Izin menunggu persetujuan</h2>
            <div class="card">
                @forelse ($pending as $p)
                    <div class="border-b border-slate-100 px-4 py-3 last:border-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $p->user->name ?? '-' }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $p->reason }}</p>
                    </div>
                @empty
                    <p class="empty !py-8">Tidak ada izin pending</p>
                @endforelse
            </div>
        </section>

        <section>
            <h2 class="mb-2 font-bold text-slate-800">Jadwal kegiatan hari ini</h2>
            <div class="card">
                @forelse ($schedules as $s)
                    <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 last:border-0">
                        <span class="w-12 text-xs font-bold" style="color: var(--brand)">{{ \Carbon\Carbon::parse($s->start_datetime)->format('H:i') }}</span>
                        <span class="text-sm text-slate-700">{{ $s->title }}</span>
                    </div>
                @empty
                    <p class="empty !py-8">Tidak ada jadwal hari ini</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const lat = @json($company->latitude), lng = @json($company->longitude);
    const box = document.getElementById('prayer');
    const msg = document.getElementById('prayer-msg');

    if (!lat || !lng || (+lat === 0 && +lng === 0)) {
        msg.textContent = 'Isi lokasi (latitude/longitude) lembaga di pengaturan untuk menampilkan jadwal sholat.';
        return;
    }

    const names = [['Fajr', 'Subuh'], ['Dhuhr', 'Dzuhur'], ['Asr', 'Ashar'], ['Maghrib', 'Maghrib'], ['Isha', 'Isya']];
    const d = new Date();
    const dmy = [String(d.getDate()).padStart(2, '0'), String(d.getMonth() + 1).padStart(2, '0'), d.getFullYear()].join('-');
    const tz = @json($company->timezone ?: 'Asia/Jakarta');

    fetch(`https://api.aladhan.com/v1/timings/${dmy}?latitude=${lat}&longitude=${lng}&method=20&timezonestring=${encodeURIComponent(tz)}`)
        .then(r => r.json())
        .then(res => {
            const t = res.data.timings;
            const list = names.map(([k, label]) => ({ label, time: t[k].slice(0, 5) }));

            function render() {
                const now = new Date();
                const mins = now.getHours() * 60 + now.getMinutes();
                let next = list.findIndex(p => { const [h, m] = p.time.split(':'); return +h * 60 + +m > mins; });
                let diff = null;
                if (next === -1) { next = 0; diff = (+list[0].time.slice(0, 2) * 60 + +list[0].time.slice(3)) + 1440 - mins; }
                else { diff = (+list[next].time.slice(0, 2) * 60 + +list[next].time.slice(3)) - mins; }

                box.innerHTML = '<div class="flex items-center gap-1">' +
                    list.map((p, i) => `<div class="flex-1 text-center"><p class="text-[10px] text-slate-400">${p.label}</p>` +
                        `<p class="text-sm ${i === next ? 'font-semibold' : 'text-slate-700'}" ${i === next ? 'style="color:var(--brand)"' : ''}>${p.time}</p></div>`).join('') +
                    `</div><div class="mt-2 rounded-xl bg-slate-50 py-2.5 text-center"><p class="text-[10px] text-slate-500">Berikutnya</p>` +
                    `<p class="font-semibold" style="color:var(--brand)">${list[next].label}</p><p class="text-[11px] text-slate-500">${diff} mnt</p></div>`;
            }
            render();
            setInterval(render, 30000);
        })
        .catch(() => { msg.textContent = 'Jadwal sholat belum bisa dimuat (cek koneksi).'; });
})();
</script>
@endpush
