@extends('layouts.company')

@section('title', 'Dashboard HR')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- Header --}}
    <div class="bg-blue-800 px-5 pt-6 pb-8 rounded-b-3xl">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-white text-2xl font-bold">Dashboard HR</h1>
                <p class="text-blue-200 text-sm mt-1">{{ $company->name }}</p>
            </div>

            <button type="button" id="btn-refresh-dashboard" data-refresh-url="{{ route('company.dashboard.refresh') }}"
                class="bg-blue-700/60 hover:bg-blue-700 text-white rounded-xl w-11 h-11 flex items-center justify-center transition"
                title="Refresh">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Konten (di-refresh via AJAX) --}}
    <div id="dashboard-content" class="px-5 -mt-4">
        @include('pages.company.dashboard_content')
    </div>
</div>

<script>
document.getElementById('btn-refresh-dashboard').addEventListener('click', function() {
    const btn = this;
    const icon = btn.querySelector('svg');
    icon.classList.add('animate-spin');

    fetch(btn.dataset.refreshUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then((res) => res.text())
        .then((html) => {
            document.getElementById('dashboard-content').innerHTML = html;
        })
        .catch(() => {
            alert('Gagal memuat ulang dashboard. Coba lagi.');
        })
        .finally(() => {
            icon.classList.remove('animate-spin');
        });
});
</script>
@endsection