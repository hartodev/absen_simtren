@extends('layouts.superadmin')
@section('title', $title ?? 'Segera Hadir')
@section('content')
<div class="card">
  <div class="card-body text-center py-5">
    <h4>{{ $title ?? 'Modul ini' }}</h4>
    <p class="text-muted">Fitur ini belum diimplementasikan pada tahap sekarang. Fokus saat ini ada di alur inti: tenant, login, dan dashboard per role.</p>
    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a>
  </div>
</div>
@endsection
