{{-- Beranda HIJAU: dashboard TPQ (ustadz) --}}
@extends('layouts.mobile')
@section('title', 'Beranda')

@section('header')
<header class="px-5 pb-9 pt-8 text-white" style="background: var(--brand)">
    <div class="flex items-start justify-between">
        <div class="min-w-0">
            <p class="text-sm text-white/80">Assalamu'alaikum,</p>
            <h1 class="mt-0.5 truncate text-2xl font-bold">{{ $me->name }}</h1>
            <p class="text-xs text-white/70">{{ $company->name }}</p>
        </div>
        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full border-2 border-white/50 bg-white/10 text-lg font-semibold">
            {{ strtoupper(mb_substr($me->name, 0, 2)) }}
        </span>
    </div>

    <a href="{{ route($rp.'attendances.index') }}"
       class="mt-4 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-medium">
        <span class="h-2.5 w-2.5 rounded-full {{ $attToday?->time_in ? 'bg-emerald-300' : 'bg-white/60' }}"></span>
        @if ($attToday?->time_in)
            Sudah Absen {{ \Illuminate\Support\Str::substr($attToday->time_in, 0, 5) }}
        @else
            Belum Absen
        @endif
    </a>
</header>
@endsection

@section('content')
@php
    $statCards = [
        ['Hadir', $att['hadir'], 'text-emerald-700'],
        ['Telat', $att['telat'], 'text-amber-600'],
        ['Absen', $att['absen'], 'text-red-700'],
        ['Izin',  $att['izin'],  'text-slate-600'],
    ];
@endphp

<div class="grid grid-cols-4 gap-2.5">
    @foreach ($statCards as [$label, $val, $cls])
        <div class="rounded-2xl bg-white py-3 text-center shadow-sm">
            <p class="text-2xl font-medium {{ $cls }}">{{ $val }}</p>
            <p class="text-[11px] text-slate-400">{{ $label }}</p>
        </div>
    @endforeach
</div>

{{-- Jadwal sholat (dimuat di browser dari api.aladhan.com, metode Kemenag RI) --}}
<div class="mt-6">
    <h2 class="font-medium text-slate-800">Jadwal sholat</h2>
    <p class="text-xs text-slate-400">{{ $company->address ?: 'Lokasi lembaga' }}</p>
</div>
<div id="prayer" class="mt-2 rounded-2xl bg-white p-3 shadow-sm">
    <p id="prayer-msg" class="px-1 py-3 text-sm text-slate-400">Memuat jadwal sholat…</p>
</div>

<div class="mt-6 flex items-center justify-between">
    <h2 class="font-medium text-slate-800">Mutaba'ah hari ini</h2>
    <a href="{{ route($rp.'mutabaah.index') }}" class="text-sm font-medium" style="color: var(--brand)">Rekap</a>
</div>
<div class="mt-2 rounded-2xl bg-white p-4 shadow-sm">
    <div class="grid grid-cols-3 divide-x divide-slate-100 text-center">
        <div><p class="text-2xl font-medium" style="color: var(--brand)">{{ $mutabaah['total'] }}</p><p class="text-[11px] text-slate-400">Total sesi</p></div>
        <div><p class="text-2xl font-medium text-amber-600">{{ $mutabaah['belum'] }}</p><p class="text-[11px] text-slate-400">Belum paraf</p></div>
        <div><p class="text-2xl font-medium" style="color: var(--brand)">{{ $mutabaah['sudah'] }}</p><p class="text-[11px] text-slate-400">Sudah paraf</p></div>
    </div>
    <div class="mt-3 flex items-center gap-3">
        <div class="h-2 flex-1 overflow-hidden rounded-full bg-emerald-50">
            <div class="h-full rounded-full" style="width: {{ $mutabaah['persen'] }}%; background: var(--brand)"></div>
        </div>
        <span class="text-xs text-slate-500">{{ $mutabaah['persen'] }}%</span>
    </div>
</div>

<div class="mt-6"><h2 class="font-medium text-slate-800">Izin menunggu persetujuan</h2></div>
<div class="mt-2 rounded-2xl bg-white shadow-sm">
    @forelse ($pending as $p)
        <div class="border-b border-slate-100 px-4 py-3 last:border-0">
            <p class="text-sm font-semibold text-slate-800">{{ $p->user->name ?? '-' }}</p>
            <p class="truncate text-xs text-slate-500">{{ $p->reason }}</p>
        </div>
    @empty
        <p class="px-4 py-5 text-sm text-slate-400">Tidak ada izin pending</p>
    @endforelse
</div>

<div class="mt-6"><h2 class="font-medium text-slate-800">Jadwal kegiatan hari ini</h2></div>
<div class="mt-2 rounded-2xl bg-white shadow-sm">
    @forelse ($schedules as $s)
        <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 last:border-0">
            <span class="text-xs font-semibold" style="color: var(--brand)">{{ \Carbon\Carbon::parse($s->start_datetime)->format('H:i') }}</span>
            <span class="text-sm text-slate-700">{{ $s->title }}</span>
        </div>
    @empty
        <p class="px-4 py-5 text-sm text-slate-400">Tidak ada jadwal hari ini</p>
    @endforelse
</div>

<div class="mt-6"><h2 class="font-medium text-slate-800">Ringkasan {{ $monthLabel }}</h2></div>
<div class="mt-2 grid grid-cols-3 gap-2.5 rounded-2xl bg-white p-3 shadow-sm">
    @foreach ([['Total santri', $summary['santri']], ['Total hadir', $summary['hadir']], ["Sesi mutaba'ah", $summary['sesi']]] as [$l, $v])
        <div class="rounded-xl bg-[#f1eee6] py-3 text-center">
            <p class="text-xl font-medium" style="color: var(--brand)">{{ $v }}</p>
            <p class="text-[11px] text-slate-500">{{ $l }}</p>
        </div>
    @endforeach
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
                    `</div><div class="mt-2 rounded-xl bg-emerald-50 py-2 text-center"><p class="text-[10px] text-slate-500">Berikutnya</p>` +
                    `<p class="font-semibold" style="color:var(--brand)">${list[next].label}</p><p class="text-[11px] text-slate-500">${diff} mnt</p></div>`;
            }
            render();
            setInterval(render, 30000);
        })
        .catch(() => { msg.textContent = 'Jadwal sholat belum bisa dimuat (cek koneksi).'; });
})();
</script>
@endpush
