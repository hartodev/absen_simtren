<?php

namespace App\Http\Controllers\Web\Tenant\Legacy;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

/**
 * LEGACY -- tidak terdaftar di routes/web.php (sisa desain lama). Aman dihapus
 * kalau sudah pasti tidak dipakai; view-nya ada di resources/views/_legacy.
 */
class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with('kelas')->latest()->paginate(20);
        return view('_legacy.tenant.siswa.index', compact('siswa'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('_legacy.tenant.siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        // tenant_id otomatis terisi lewat trait BelongsToTenant, tidak perlu ditulis manual
        Siswa::create($request->only('nama', 'nis', 'kelas_id'));

        return redirect()->route('tenant.siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        return view('_legacy.tenant.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $siswa->update($request->only('nama', 'nis', 'kelas_id'));

        return redirect()->route('tenant.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('tenant.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}