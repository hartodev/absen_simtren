@extends('layouts.employee')

@section('title', 'Laporan Harian')

@section('breadcrumb')
<a href="{{ route('company.member.dashboard') }}">Dashboard</a>
<span class="current">Laporan Harian</span>
@endsection

@section('content')
<div class="page-title">Laporan Harian</div>
<div class="page-sub">Target pagi & pencapaian sore</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:14px">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:14px">{{ $errors->first() }}</div>
@endif

{{-- Hari ini --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Hari Ini</span>
        @if(!$todayReport)
        <span class="badge badge-gray">Belum Submit</span>
        @elseif(!$todayReport->achievement)
        <span class="badge badge-warning">Menunggu Pencapaian</span>
        @elseif($todayReport->is_achieved)
        <span class="badge badge-success">Tercapai</span>
        @else
        <span class="badge badge-danger">Tidak Tercapai</span>
        @endif
    </div>
    <div class="card-body">

        @if(!$todayReport)
        {{-- Form submit target pagi --}}
        <form method="POST" action="{{ route('company.member.daily-reports.store') }}" enctype="multipart/form-data">
            @csrf
            <label class="form-label">Target Hari Ini</label>
            <textarea name="target" rows="3" class="form-control" placeholder="Tuliskan target kerja hari ini..."
                required>{{ old('target') }}</textarea>

            <label class="form-label" style="margin-top:10px">Lampiran (opsional)</label>
            <input type="file" name="attachment" accept="image/*" class="form-control">

            <button type="submit" class="btn" style="width:100%;margin-top:14px">Submit Target Pagi</button>
        </form>

        @elseif(!$todayReport->achievement)
        <p style="font-size:13px;color:var(--c-muted);margin-bottom:12px">
            <strong>Target pagi:</strong> {{ $todayReport->target }}
        </p>

        {{-- Form submit pencapaian sore --}}
        <form method="POST" action="{{ route('company.member.daily-reports.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <label class="form-label">Pencapaian Hari Ini</label>
            <textarea name="achievement" rows="3" class="form-control"
                placeholder="Apa yang sudah dikerjakan hari ini..." required>{{ old('achievement') }}</textarea>

            <label class="form-label" style="margin-top:10px">Apakah target tercapai?</label>
            <select name="is_achieved" id="is_achieved" class="form-control" required>
                <option value="1">Ya, tercapai</option>
                <option value="0">Tidak tercapai</option>
            </select>

            <div id="reason-wrap" style="display:none;margin-top:10px">
                <label class="form-label">Alasan tidak tercapai</label>
                <textarea name="reason_not_achieved" rows="2"
                    class="form-control">{{ old('reason_not_achieved') }}</textarea>
            </div>

            <label class="form-label" style="margin-top:10px">Lampiran (opsional)</label>
            <input type="file" name="attachment" accept="image/*" class="form-control">

            <button type="submit" class="btn" style="width:100%;margin-top:14px">Submit Pencapaian</button>
        </form>

        <script>
        document.getElementById('is_achieved').addEventListener('change', function() {
            document.getElementById('reason-wrap').style.display = this.value === '0' ? 'block' : 'none';
        });
        </script>

        @else
        <p style="font-size:13px;color:var(--c-muted)"><strong>Target:</strong> {{ $todayReport->target }}</p>
        <p style="font-size:13px;color:var(--c-muted);margin-top:6px"><strong>Pencapaian:</strong>
            {{ $todayReport->achievement }}</p>
        @if(!$todayReport->is_achieved && $todayReport->reason_not_achieved)
        <p style="font-size:13px;color:var(--c-muted);margin-top:6px"><strong>Alasan:</strong>
            {{ $todayReport->reason_not_achieved }}</p>
        @endif
        @endif
    </div>
</div>

{{-- Ringkasan bulan --}}
<div class="card" style="margin-top:16px">
    <div class="card-header">
        <span class="card-title">Ringkasan Bulan Ini</span>
    </div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Total Hari</div>
                <div class="metric-val">{{ $summary['total_days'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Tercapai</div>
                <div class="metric-val">{{ $summary['achieved'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Tidak Tercapai</div>
                <div class="metric-val warning">{{ $summary['not_achieved'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Belum Isi Sore</div>
                <div class="metric-val info">{{ $summary['pending_evening'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat --}}
<div class="card" style="margin-top:16px">
    <div class="card-header">
        <span class="card-title">Riwayat</span>
    </div>
    <div class="card-body" style="padding:0">
        @forelse($reports as $r)
        <a href="{{ route('company.member.daily-reports.show', $r->id) }}" class="menu-row">
            <span>{{ \Carbon\Carbon::parse($r->date)->isoFormat('D MMM Y') }}</span>
            @if(!$r->achievement) <span class="badge badge-gray">Belum Sore</span>
            @elseif($r->is_achieved) <span class="badge badge-success">Tercapai</span>
            @else <span class="badge badge-danger">Tidak Tercapai</span>
            @endif
        </a>
        @empty
        <div class="menu-row menu-row-disabled">Belum ada laporan bulan ini.</div>
        @endforelse
    </div>
</div>
<div style="margin-top:12px">{{ $reports->links() }}</div>

<style>
.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 5px;
    color: var(--c-text, #1a1a1a)
}

.form-control {
    width: 100%;
    border: 1px solid var(--c-border, #e3e8f0);
    border-radius: 10px;
    padding: 9px 12px;
    font-size: 14px;
    font-family: inherit
}

.menu-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid var(--c-border, #eee);
    text-decoration: none;
    color: inherit;
    font-size: 14px;
    font-weight: 600
}

.menu-row:last-child {
    border-bottom: none
}

.menu-row-disabled {
    color: var(--c-muted, #8a94a6);
    font-weight: 500
}

.alert {
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600
}

.alert-success {
    background: #e0f4ea;
    color: #198754
}

.alert-danger {
    background: #fff1f1;
    color: #b34040
}
</style>
@endsection