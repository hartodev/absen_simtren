<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\ShiftGroupAssignment;
use App\Models\UserShiftOverride;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EmployeeShiftController extends Controller
{
    // ----------------------------------------------------------
    // Port 1:1 dari Api\Employee\EmployeeShiftController::resolveShiftByDate()
    // ----------------------------------------------------------
    private function resolveShiftByDate(Request $request, string $date): array
    {
        $user      = $request->user();
        $companyId = (int) $user->company_id;
        $userId    = (int) $user->id;
        $d         = Carbon::parse($date)->toDateString();

        // 1) Override user (paling prioritas)
        $override = UserShiftOverride::query()
            ->where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $d)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhereDate('end_date', '>=', $d))
            ->with(['shift:id,name,start_time,end_time,grace_period_minutes,is_default'])
            ->orderByDesc('start_date')
            ->first();

        if ($override && $override->shift) {
            return [
                'source' => 'override',
                'shift'  => $override->shift,
                'meta'   => [
                    'start_date' => optional($override->start_date)->toDateString(),
                    'end_date'   => optional($override->end_date)->toDateString(),
                    'reason'     => $override->reason,
                ],
            ];
        }

        // 2) Group assignment
        $groupIds = $user->shiftGroups()
            ->where(fn ($q) => $q->whereNull('shift_group_users.start_date')->orWhereDate('shift_group_users.start_date', '<=', $d))
            ->where(fn ($q) => $q->whereNull('shift_group_users.end_date')->orWhereDate('shift_group_users.end_date', '>=', $d))
            ->pluck('shift_groups.id')
            ->toArray();

        if (! empty($groupIds)) {
            $assignment = ShiftGroupAssignment::query()
                ->where('company_id', $companyId)
                ->whereIn('shift_group_id', $groupIds)
                ->whereDate('start_date', '<=', $d)
                ->where(fn ($q) => $q->whereNull('end_date')->orWhereDate('end_date', '>=', $d))
                ->with(['shift:id,name,start_time,end_time,grace_period_minutes,is_default', 'group:id,name'])
                ->orderByDesc('start_date')
                ->first();

            if ($assignment && $assignment->shift) {
                return [
                    'source' => 'group_assignment',
                    'shift'  => $assignment->shift,
                    'meta'   => [
                        'shift_group_name' => optional($assignment->group)->name,
                        'note'             => $assignment->note,
                    ],
                ];
            }
        }

        // 3) Default shift fallback
        $defaultShift = Shift::query()
            ->where('company_id', $companyId)
            ->where('is_default', true)
            ->select('id', 'name', 'start_time', 'end_time', 'grace_period_minutes', 'is_default')
            ->first();

        if ($defaultShift) {
            return ['source' => 'default_shift', 'shift' => $defaultShift, 'meta' => null];
        }

        return ['source' => 'none', 'shift' => null, 'meta' => null];
    }

    // ----------------------------------------------------------
    // GET /employee/shifts?start=YYYY-MM-DD&end=YYYY-MM-DD
    // Default: minggu berjalan (Senin - Minggu)
    // ----------------------------------------------------------
    public function schedule(Request $request): View
    {
        $start = $request->filled('start')
            ? Carbon::parse($request->start)->startOfDay()
            : Carbon::now()->startOfWeek();

        $end = $request->filled('end')
            ? Carbon::parse($request->end)->startOfDay()
            : $start->copy()->addDays(6);

        if ($start->diffInDays($end) > 62) {
            $end = $start->copy()->addDays(62);
        }

        $items  = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $d        = $cursor->toDateString();
            $resolved = $this->resolveShiftByDate($request, $d);

            $items[] = [
                'date'   => $d,
                'source' => $resolved['source'],
                'shift'  => $resolved['shift'],
                'meta'   => $resolved['meta'],
            ];

            $cursor->addDay();
        }

        return view('pages.employee.shift_schedule', [
            'items' => $items,
            'start' => $start->toDateString(),
            'end'   => $end->toDateString(),
        ]);
    }
}