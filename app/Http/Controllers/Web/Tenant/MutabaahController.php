<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Models\MutabaahYaumiyah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/** Mutaba'ah Yaumiyah (progres ngaji) -- dashboard TPQ hijau. */
class MutabaahController extends TenantAdminController
{
    public function index(Request $request)
    {
        $date = Carbon::parse($request->get('date', today()->toDateString()));

        $records = MutabaahYaumiyah::with(['santri:id,name', 'ustadz:id,name', 'signer:id,name'])
            ->where('company_id', $this->cid())->whereDate('tanggal', $date)
            ->orderBy('sesi')->orderBy('id')->get();

        // Rekap bulan berjalan per santri
        $rekap = MutabaahYaumiyah::with('santri:id,name')
            ->where('company_id', $this->cid())->bulan($date->month, $date->year)
            ->selectRaw('santri_id, count(*) as sesi, sum(is_lanjut) as lanjut, sum(signed_by is not null) as diparaf')
            ->groupBy('santri_id')->get();

        return $this->view('mutabaah.index', [
            'date'    => $date,
            'records' => $records,
            'rekap'   => $rekap,
            'santri'  => User::where('company_id', $this->cid())->where('role', 'santri')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $cid = $this->cid();

        $data = $request->validate([
            'santri_id'      => ['required', Rule::exists('users', 'id')->where('company_id', $cid)->where('role', 'santri')],
            'tanggal'        => ['required', 'date'],
            'sesi'           => ['required', 'in:pagi,sore'],
            'kitab'          => ['required', 'in:iqro,quran'],
            'jilid'          => ['required', 'integer', 'between:1,7'],
            'halaman_dari'   => ['required', 'integer', 'min:1', 'max:604'],
            'halaman_sampai' => ['nullable', 'integer', 'gte:halaman_dari', 'max:604'],
            'keterangan'     => ['required', Rule::in(['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-'])],
            'catatan'        => ['nullable', 'string', 'max:500'],
        ]);

        $exists = MutabaahYaumiyah::where('company_id', $cid)->where('santri_id', $data['santri_id'])
            ->whereDate('tanggal', $data['tanggal'])->where('sesi', $data['sesi'])->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Santri ini sudah punya catatan untuk sesi tersebut.');
        }

        MutabaahYaumiyah::create($data + ['company_id' => $cid, 'ustadz_id' => Auth::id()]);

        return $this->to('mutabaah.index', ['date' => $data['tanggal']])->with('success', "Catatan mutaba'ah tersimpan.");
    }

    public function sign(int $id)
    {
        $record = MutabaahYaumiyah::where('company_id', $this->cid())->findOrFail($id);
        $record->update(['signed_by' => Auth::id(), 'signed_at' => now()]);

        return back()->with('success', 'Paraf diberikan.');
    }
}
