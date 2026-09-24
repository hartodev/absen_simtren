<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * STUB SEMENTARA -- modul ini belum diimplementasikan (billing/subscription
 * sengaja ditunda, fokus tahap ini ada di alur inti tenant + attendance).
 * Semua method mengembalikan halaman "coming soon" supaya route tidak 500,
 * bukan logika bisnis sungguhan.
 */
class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.superadmin.coming-soon', ['title' => 'SupportTicketController']);
    }

    public function show(Request $request, $id = null)
    {
        return view('pages.superadmin.coming-soon', ['title' => 'SupportTicketController']);
    }

    public function create(Request $request)
    {
        return view('pages.superadmin.coming-soon', ['title' => 'SupportTicketController']);
    }

    public function store(Request $request)
    {
        return back()->with('error', 'Fitur ini belum tersedia.');
    }

    public function edit(Request $request, $id = null)
    {
        return view('pages.superadmin.coming-soon', ['title' => 'SupportTicketController']);
    }

    public function update(Request $request, $id = null)
    {
        return back()->with('error', 'Fitur ini belum tersedia.');
    }

    public function destroy(Request $request, $id = null)
    {
        return back()->with('error', 'Fitur ini belum tersedia.');
    }

    public function __call($name, $args)
    {
        return view('pages.superadmin.coming-soon', ['title' => 'SupportTicketController']);
    }
}
