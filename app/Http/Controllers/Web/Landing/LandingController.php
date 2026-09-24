<?php

namespace App\Http\Controllers\Web\Landing;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class LandingController extends Controller
{
    public function index()
    {
        $mitra = Tenant::where('status', 'aktif')
            ->where('publik', true)
            ->orderByDesc('created_at')
            ->get(['nama_lembaga', 'subdomain']);

        // Bentuk data untuk JS di sini (bukan di Blade), supaya @json() di view
        // hanya menerima 1 variabel tanpa koma di dalam ekspresinya.
        $directoryData = $mitra->map(function ($t) {
            $initials = \Illuminate\Support\Str::of($t->nama_lembaga)
                ->explode(' ')
                ->map(fn ($w) => strtoupper($w[0] ?? ''))
                ->take(2)
                ->implode('');

            return [
                'subdomain' => $t->subdomain . '.' . config('app.main_domain'),
                'name' => $t->nama_lembaga,
                'type' => 'Lembaga',
                'city' => '',
                'initials' => $initials,
                'tone' => 'bg-tone-blue',
            ];
        })->values();

        return view('pages.landing.index', compact('mitra', 'directoryData'));
    }
}