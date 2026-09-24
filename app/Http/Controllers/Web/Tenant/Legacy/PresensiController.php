<?php

namespace App\Http\Controllers\Web\Tenant\Legacy;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;

/**
 * LEGACY -- tidak terdaftar di routes/web.php (sisa desain lama). Aman dihapus
 * kalau sudah pasti tidak dipakai; view-nya ada di resources/views/_legacy.
 */
class PresensiController extends Controller
{
    public function index()
    {
        $presensi = Presensi::with('siswa')->latest('tanggal')->paginate(20);
        return view('_legacy.tenant.presensi.index', compact('presensi'));
    }

    public function create()
    {
        $siswa = Siswa::orderBy('nama')->get();
        return view('_legacy.tenant.presensi.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa',
        ]);

        // tenant_id otomatis terisi lewat trait BelongsToTenant
        Presensi::create($request->only('siswa_id', 'tanggal', 'status'));

        return redirect()->route('tenant.presensi.index')->with('success', 'Presensi berhasil dicatat.');
    }

    public function edit(Presensi $presensi)
    {
        $siswa = Siswa::orderBy('nama')->get();
        return view('_legacy.tenant.presensi.edit', compact('presensi', 'siswa'));
    }

    public function update(Request $request, Presensi $presensi)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa',
        ]);

        $presensi->update($request->only('siswa_id', 'tanggal', 'status'));

        return redirect()->route('tenant.presensi.index')->with('success', 'Presensi berhasil diperbarui.');
    }

    public function destroy(Presensi $presensi)
    {
        $presensi->delete();

        return redirect()->route('tenant.presensi.index')->with('success', 'Presensi berhasil dihapus.');
    }
}