@extends('layouts.company')
@section('title', 'Absen Sekarang')
@section('nav')
  <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
  <a href="{{ route('tenant.karyawan.index') }}">Karyawan</a>
  <a href="{{ route('tenant.absensi-karyawan.index') }}">Absensi</a>
  <a href="{{ route('tenant.absensi-karyawan.absen') }}" class="text-[#2563eb]">Absen Sekarang</a>
@endsection
@section('content')

@if (session('success'))
  <div class="mb-4 rounded-lg bg-[#e0f4ea] px-3 py-2 text-xs font-semibold text-[#198754]">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="mb-4 rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">{{ session('error') }}</div>
@endif

<div class="rounded-2xl p-6 text-white shadow-lg" style="background: var(--tenant-color)">
    <h1 class="text-xl font-extrabold">Absen Karyawan</h1>
    <p class="mt-1 text-sm text-white/85" id="jam-sekarang">Memuat jam...</p>
</div>

<div class="mt-6 max-w-lg rounded-xl bg-white p-6 shadow-sm">
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Pilih Nama Karyawan</span>
    <select id="karyawan_id" class="form-control h-12">
      <option value="">-- Pilih Karyawan --</option>
      @foreach ($karyawan as $k)
        @php $a = $absensiHariIni->get($k->id); @endphp
        <option value="{{ $k->id }}"
                data-sudah-masuk="{{ $a && $a->sudahCheckIn() ? '1' : '0' }}"
                data-sudah-pulang="{{ $a && $a->sudahCheckOut() ? '1' : '0' }}">
          {{ $k->nama }}{{ $a && $a->sudahCheckIn() ? ' (sudah absen masuk)' : '' }}
        </option>
      @endforeach
    </select>
  </label>

  <p id="status-info" class="mt-3 hidden rounded-lg bg-[#f6f8fb] px-3 py-2 text-xs text-[#405978]"></p>
  <p id="lokasi-info" class="mt-2 text-xs text-[#71839b]">Mengambil lokasi...</p>

  <div class="mt-5 grid grid-cols-2 gap-3">
    <form id="form-checkin" method="POST" action="{{ route('tenant.absensi-karyawan.check-in') }}">
      @csrf
      <input type="hidden" name="karyawan_id" id="checkin-karyawan-id">
      <input type="hidden" name="lokasi" id="checkin-lokasi">
      <button type="submit" id="btn-checkin" disabled
              class="button-lift w-full rounded-xl bg-[#2563eb] py-3 text-sm font-bold text-white disabled:opacity-40">
        Absen Masuk
      </button>
    </form>
    <form id="form-checkout" method="POST" action="{{ route('tenant.absensi-karyawan.check-out') }}">
      @csrf
      <input type="hidden" name="karyawan_id" id="checkout-karyawan-id">
      <input type="hidden" name="lokasi" id="checkout-lokasi">
      <button type="submit" id="btn-checkout" disabled
              class="button-lift w-full rounded-xl border border-[#2563eb] py-3 text-sm font-bold text-[#2563eb] disabled:opacity-40">
        Absen Pulang
      </button>
    </form>
  </div>
</div>

<div class="mt-6 rounded-xl bg-white p-5 shadow-sm">
  <p class="text-sm font-bold text-[#102b69]">Status Absen Hari Ini ({{ \Illuminate\Support\Carbon::parse($today)->format('d M Y') }})</p>
  <div class="mt-3 overflow-hidden rounded-lg border border-[#eef2f7]">
    <table class="w-full text-sm">
      <thead class="bg-[#f6f8fb] text-left text-xs font-bold uppercase text-[#71839b]">
        <tr><th class="px-4 py-2">Nama</th><th class="px-4 py-2">Masuk</th><th class="px-4 py-2">Pulang</th></tr>
      </thead>
      <tbody class="divide-y divide-[#eef2f7]">
        @forelse ($karyawan as $k)
          @php $a = $absensiHariIni->get($k->id); @endphp
          <tr>
            <td class="px-4 py-2 font-semibold text-[#102b69]">{{ $k->nama }}</td>
            <td class="px-4 py-2 text-[#71839b]">{{ $a && $a->jam_masuk ? \Illuminate\Support\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}</td>
            <td class="px-4 py-2 text-[#71839b]">{{ $a && $a->jam_pulang ? \Illuminate\Support\Carbon::parse($a->jam_pulang)->format('H:i') : '-' }}</td>
          </tr>
        @empty
          <tr><td colspan="3" class="px-4 py-4 text-center text-[#8192aa]">Belum ada karyawan aktif.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();

  // Jam realtime
  const jamEl = document.getElementById('jam-sekarang');
  function tickJam() {
    jamEl.textContent = new Date().toLocaleString('id-ID', {
      weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
      hour: '2-digit', minute: '2-digit', second: '2-digit'
    });
  }
  tickJam();
  setInterval(tickJam, 1000);

  // Ambil lokasi sekali di awal
  const lokasiInfo = document.getElementById('lokasi-info');
  let lokasiString = '';

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        lokasiString = pos.coords.latitude + ',' + pos.coords.longitude;
        lokasiInfo.textContent = 'Lokasi terdeteksi ✓';
        lokasiInfo.classList.add('text-[#198754]');
      },
      () => {
        lokasiInfo.textContent = 'Lokasi tidak dapat diakses (absen tetap bisa dilakukan tanpa lokasi).';
      }
    );
  } else {
    lokasiInfo.textContent = 'Perangkat tidak mendukung deteksi lokasi.';
  }

  // Toggle tombol sesuai status karyawan yang dipilih
  const select = document.getElementById('karyawan_id');
  const btnIn = document.getElementById('btn-checkin');
  const btnOut = document.getElementById('btn-checkout');
  const statusInfo = document.getElementById('status-info');

  select.addEventListener('change', () => {
    const opt = select.options[select.selectedIndex];
    const id = select.value;

    document.getElementById('checkin-karyawan-id').value = id;
    document.getElementById('checkout-karyawan-id').value = id;

    if (!id) {
      btnIn.disabled = true;
      btnOut.disabled = true;
      statusInfo.classList.add('hidden');
      return;
    }

    const sudahMasuk = opt.dataset.sudahMasuk === '1';
    const sudahPulang = opt.dataset.sudahPulang === '1';

    btnIn.disabled = sudahMasuk;
    btnOut.disabled = !sudahMasuk || sudahPulang;

    statusInfo.classList.remove('hidden');
    if (sudahPulang) {
      statusInfo.textContent = 'Karyawan ini sudah absen masuk & pulang hari ini.';
    } else if (sudahMasuk) {
      statusInfo.textContent = 'Sudah absen masuk. Siap absen pulang.';
    } else {
      statusInfo.textContent = 'Belum absen hari ini.';
    }
  });

  // Isi lokasi ke form tepat sebelum submit
  ['form-checkin', 'form-checkout'].forEach((formId) => {
    document.getElementById(formId).addEventListener('submit', (e) => {
      if (!select.value) {
        e.preventDefault();
        alert('Pilih nama karyawan terlebih dahulu.');
        return;
      }
      const lokasiField = formId === 'form-checkin' ? 'checkin-lokasi' : 'checkout-lokasi';
      document.getElementById(lokasiField).value = lokasiString;
    });
  });
});
</script>
@endpush
