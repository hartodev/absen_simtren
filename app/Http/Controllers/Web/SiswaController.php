<?php

namespace App\Http\Controllers\Web;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with('kelas')->latest()->paginate(20);
        return view('tenant.siswa.index', compact('siswa'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('tenant.siswa.create', compact('kelas'));
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
        return view('tenant.siswa.edit', compact('siswa', 'kelas'));
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