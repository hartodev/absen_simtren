@extends('layouts.employee')

@section('title', 'Jadwal Shift')

@section('breadcrumb')
<span class="current">Jadwal Shift</span>
@endsection

@section('content')
<div class="page-title">Jadwal Shift</div>
<div class="page-sub">Periode {{ \Carbon\Carbon::parse($start)->isoFormat('D MMMM Y') }} &ndash;
    {{ \Carbon\Carbon::parse($end)->isoFormat('D MMMM Y') }}</div>

@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

<div class="card" style="margin-top:16px">
    <form method="GET" action="{{ url()->current() }}"
        style="display:flex; gap:10px; align-items:end; padding:16px; flex-wrap:wrap;">
        <div>
            <label class="metric-label" style="display:block; margin-bottom:4px;">Dari tanggal</label>
            <input type="date" name="start" value="{{ $start }}" class="form-control">
        </div>
        <div>
            <label class="metric-label" style="display:block; margin-bottom:4px;">Sampai tanggal</label>
            <input type="date" name="end" value="{{ $end }}" class="form-control">
        </div>
        <div>
            <button type="submit" class="button-lift"
                style="border-radius:10px; background:#2563eb; color:#fff; font-weight:700; font-size:14px; padding:10px 18px; border:none;">
                Tampilkan
            </button>
        </div>
    </form>
</div>

<div class="card" style="margin-top:16px">
    <div class="card-header">
        <span class="card-title">Rincian Jadwal</span>
    </div>
    <div class="card-body" style="padding:0; overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #e5eaf1;">
                    <th style="padding:12px 16px; font-size:12px; color:#71839b;">Tanggal</th>
                    <th style="padding:12px 16px; font-size:12px; color:#71839b;">Shift</th>
                    <th style="padding:12px 16px; font-size:12px; color:#71839b;">Jam</th>
                    <th style="padding:12px 16px; font-size:12px; color:#71839b;">Sumber</th>
                    <th style="padding:12px 16px; font-size:12px; color:#71839b;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr style="border-bottom:1px solid #f1f4f9;">
                    <td style="padding:12px 16px; font-weight:600;">
                        {{ \Carbon\Carbon::parse($item['date'])->isoFormat('ddd, D MMM Y') }}
                    </td>
                    <td style="padding:12px 16px;">
                        {{ $item['shift']->name ?? '-' }}
                    </td>
                    <td style="padding:12px 16px;">
                        @if($item['shift'])
                        {{ \Carbon\Carbon::parse($item['shift']->start_time)->format('H:i') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($item['shift']->end_time)->format('H:i') }}
                        @else
                        -
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($item['source'] === 'override')
                        <span class="badge badge-warning">Override</span>
                        @elseif($item['source'] === 'group_assignment')
                        <span class="badge badge-success">Group</span>
                        @elseif($item['source'] === 'default_shift')
                        <span class="badge badge-gray">Default</span>
                        @else
                        <span class="badge badge-danger">Tidak ada shift</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px; color:#71839b; font-size:13px;">
                        @if($item['source'] === 'override' && $item['meta'])
                        {{ $item['meta']['reason'] ?? '-' }}
                        @elseif($item['source'] === 'group_assignment' && $item['meta'])
                        {{ $item['meta']['shift_group_name'] ?? '-' }}
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:24px 16px; text-align:center; color:#71839b;">
                        Tidak ada data jadwal pada periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection