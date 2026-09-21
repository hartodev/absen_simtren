@extends('layouts.tenant')
@section('title', 'Absensi Saya')
@section('nav')
  <a href="{{ url('/employee/dashboard') }}">Dashboard</a>
  <a href="{{ url('/' . request()->segment(1) . '/attendance') }}" class="text-blue-600">Absensi</a>
@endsection
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Absensi Saya</h1>

<div class="mt-5 rounded-xl bg-white p-5 shadow-sm">
  <p class="text-sm font-bold text-gray-500">Status Hari Ini</p>
  @if ($status['is_checked_in'])
    <p class="mt-2 text-sm">Check-in: <strong>{{ $status['check_in_time'] }}</strong></p>
    @if ($status['is_checked_out'])
      <p class="text-sm">Check-out: <strong>{{ $status['check_out_time'] }}</strong></p>
    @else
      <form method="POST" action="{{ url('/' . request()->segment(1) . '/attendance/checkout') }}" class="mt-3" id="checkoutForm">
        @csrf
        <input type="hidden" name="latitude" id="co_lat"><input type="hidden" name="longitude" id="co_lng">
        <button type="button" onclick="doGeo('checkoutForm')" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white">Check-out Sekarang</button>
      </form>
    @endif
  @else
    <form method="POST" action="{{ url('/' . request()->segment(1) . '/attendance/checkin') }}" class="mt-3" id="checkinForm">
      @csrf
      <input type="hidden" name="latitude" id="ci_lat"><input type="hidden" name="longitude" id="ci_lng">
      <button type="button" onclick="doGeo('checkinForm')" class="rounded-lg bg-green-600 px-4 py-2 text-xs font-bold text-white">Check-in Sekarang</button>
    </form>
  @endif
</div>

<div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-5">
  <div class="rounded-lg bg-white p-4 text-center shadow-sm"><p class="text-xs text-gray-400">Hadir</p><p class="font-bold">{{ $summary['present'] }}</p></div>
  <div class="rounded-lg bg-white p-4 text-center shadow-sm"><p class="text-xs text-gray-400">Telat</p><p class="font-bold">{{ $summary['late'] }}</p></div>
  <div class="rounded-lg bg-white p-4 text-center shadow-sm"><p class="text-xs text-gray-400">Izin</p><p class="font-bold">{{ $summary['permitted'] }}</p></div>
  <div class="rounded-lg bg-white p-4 text-center shadow-sm"><p class="text-xs text-gray-400">Cuti</p><p class="font-bold">{{ $summary['on_leave'] }}</p></div>
  <div class="rounded-lg bg-white p-4 text-center shadow-sm"><p class="text-xs text-gray-400">Alpha</p><p class="font-bold">{{ $summary['absent'] }}</p></div>
</div>

<div class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left text-xs font-bold uppercase text-gray-400">
      <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Masuk</th><th class="px-4 py-3">Pulang</th><th class="px-4 py-3">Status</th></tr>
    </thead>
    <tbody class="divide-y">
      @forelse ($history['data'] as $row)
        <tr>
          <td class="px-4 py-3">{{ $row['date'] }}</td>
          <td class="px-4 py-3">{{ $row['check_in_time'] ?? '-' }}</td>
          <td class="px-4 py-3">{{ $row['check_out_time'] ?? '-' }}</td>
          <td class="px-4 py-3">{{ ucfirst($row['status']) }}</td>
        </tr>
      @empty
        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Belum ada data.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<script>
function doGeo(formId) {
  navigator.geolocation.getCurrentPosition(function(pos) {
    const form = document.getElementById(formId);
    form.querySelector('input[name=latitude]').value = pos.coords.latitude;
    form.querySelector('input[name=longitude]').value = pos.coords.longitude;
    form.submit();
  }, function() { alert('Gagal mengambil lokasi. Izinkan akses lokasi di browser.'); });
}
</script>
@endsection
