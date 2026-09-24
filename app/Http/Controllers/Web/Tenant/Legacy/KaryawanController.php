<?php

namespace App\Http\Controllers\Web\Tenant\Legacy;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;

/**
 * LEGACY -- tidak terdaftar di routes/web.php (sisa desain lama). Aman dihapus
 * kalau sudah pasti tidak dipakai; view-nya ada di resources/views/_legacy.
 */
class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::with('absensiHariIni')->latest()->paginate(20);

        return view('_legacy.pages.tenant.karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        return view('_legacy.pages.tenant.karyawan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'departemen' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
        ]);

        // tenant_id otomatis terisi lewat trait BelongsToTenant, tidak perlu ditulis manual
        Karyawan::create($request->only(
            'nama', 'nip', 'jabatan', 'departemen', 'no_hp', 'email', 'jam_masuk', 'jam_pulang'
        ));

        return redirect()->route('tenant.karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        return view('_legacy.pages.tenant.karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'departemen' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
            'status_aktif' => 'nullable|boolean',
        ]);

        $data = $request->only(
            'nama', 'nip', 'jabatan', 'departemen', 'no_hp', 'email', 'jam_masuk', 'jam_pulang'
        );
        $data['status_aktif'] = $request->boolean('status_aktif');

        $karyawan->update($data);

        return redirect()->route('tenant.karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('tenant.karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
    }
}