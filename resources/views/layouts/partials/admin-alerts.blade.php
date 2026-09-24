@if (session('success'))
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
        <i data-lucide="check-circle-2" class="h-4 w-4 shrink-0"></i> {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
        <i data-lucide="alert-circle" class="h-4 w-4 shrink-0"></i> {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
