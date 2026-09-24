@extends('layouts.employee')

@section('title', 'Detail Performa')

@section('breadcrumb')
<a href="{{ route('company.member.performance-scores.index') }}">Performa</a>
<span class="current">Detail</span>
@endsection

@section('content')
<div class="page-title">{{ \Carbon\Carbon::create($score->year, $score->month, 1)->isoFormat('MMMM Y') }}</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Skor Akhir</span>
        <span class="badge badge-info">{{ $score->final_score }}</span>
    </div>
    <div class="card-body">
        {{-- Tampilkan atribut skor lain secara generik — sesuaikan label kalau nama kolomnya beda --}}
        <div class="metrics">
            @foreach($score->getAttributes() as $key => $value)
            @continue(in_array($key, ['id', 'company_id', 'user_id', 'month', 'year', 'final_score', 'created_at',
            'updated_at', 'deleted_at']))
            @continue(!is_numeric($value))
            <div class="metric">
                <div class="metric-label">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                <div class="metric-val">{{ $value }}</div>
            </div>
            @endforeach
        </div>

        @if(!empty($score->note))
        <p style="font-size:13px;color:var(--c-muted);margin-top:16px"><strong>Catatan:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $score->note }}</p>
        @endif

        <a href="{{ route('company.member.performance-scores.index') }}" class="btn btn-outline" style="margin-top:20px">
            &larr; Kembali
        </a>
    </div>
</div>
@endsection