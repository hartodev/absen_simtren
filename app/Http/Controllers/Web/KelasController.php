<?php

namespace App\Http\Controllers\Web;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount('siswa')->latest()->paginate(20);
        return view('tenant.kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('tenant.kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
        ]);

        // tenant_id otomatis terisi lewat trait BelongsToTenant
        Kelas::create($request->only('nama_kelas'));

        return redirect()->route('tenant.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kela)
    {
        return view('tenant.kelas.edit', ['kelas' => $kela]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
        ]);

        $kela->update($request->only('nama_kelas'));

        return redirect()->route('tenant.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return redirect()->route('tenant.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}