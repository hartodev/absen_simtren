<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Support\SchoolModules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Halaman "segera hadir" untuk kartu grid yang modulnya belum punya halaman
 * sendiri -- supaya kartu tidak 404. Kalau tabelnya sudah ada, ditampilkan
 * jumlah datanya.
 */
class ModuleController extends TenantAdminController
{
    public function show(string $slug)
    {
        $module = SchoolModules::find($slug);
        abort_unless($module, 404);
        abort_if(! empty($module['boarding']) && ! $this->company()->hasBoarding(), 404);

        $count = null;
        $table = $module['table'] ?? null;

        if ($table && Schema::hasTable($table)) {
            $q = DB::table($table);
            if (Schema::hasColumn($table, 'company_id')) {
                $count = $q->where('company_id', $this->cid())->count();
            } elseif ($table === 'class_teachers') {
                $count = $q->whereIn('class_id', fn ($s) => $s->select('id')->from('class_rooms')->where('company_id', $this->cid()))->count();
            }
        }

        return $this->view('modules.show', compact('module', 'count'));
    }
}
