<?php

namespace App\Http\Controllers\Web\Tenant\Legacy;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

/**
 * LEGACY -- tidak terdaftar di routes/web.php (sisa desain lama). Aman dihapus
 * kalau sudah pasti tidak dipakai; view-nya ada di resources/views/_legacy.
 */
class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount('siswa')->latest()->paginate(20);
        return view('_legacy.tenant.kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('_legacy.tenant.kelas.create');
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
        return view('_legacy.tenant.kelas.edit', ['kelas' => $kela]);
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