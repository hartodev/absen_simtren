<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeNotesController extends Controller
{
    // ----------------------------------------------------------
    // GET /employee/notes
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $companyId = Auth::user()->company_id;
        $userId    = Auth::id();

        $query = Note::with('creator:id,name')
            ->where('company_id', $companyId)
            ->where('user_id', $userId);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read === '1');
        }
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('note', 'like', "%{$keyword}%");
            });
        }

        $notes = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $summary = Note::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total_notes,
                SUM(CASE WHEN type = "warning" THEN 1 ELSE 0 END) as total_warning,
                SUM(CASE WHEN type = "praise" THEN 1 ELSE 0 END) as total_praise,
                SUM(CASE WHEN type = "performance" THEN 1 ELSE 0 END) as total_performance,
                SUM(CASE WHEN type = "absence" THEN 1 ELSE 0 END) as total_absence,
                SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as total_unread
            ')->first();

        return view('pages.employee.notes.index', [
            'notes'   => $notes,
            'summary' => $summary,
            'filters' => $request->only(['type', 'is_read', 'search']),
        ]);
    }

    // ----------------------------------------------------------
    // GET /employee/notes/{id} — otomatis mark as read
    // ----------------------------------------------------------
    public function show(int $id): View
    {
        $note = Note::with('creator:id,name')
            ->where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if (! $note->is_read) {
            $note->update(['is_read' => true]);
        }

        return view('pages.employee.notes.show', ['note' => $note]);
    }
}