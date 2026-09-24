<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\PerformanceScore;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePerformanceScoreController extends Controller
{
    // ----------------------------------------------------------
    // GET /employee/performance-scores
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $query = PerformanceScore::where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::id());

        if ($request->filled('month')) $query->where('month', $request->month);
        if ($request->filled('year'))  $query->where('year', $request->year);

        $scores = $query->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15)
            ->withQueryString();

        return view('pages.employee.performance_scores.index', [
            'scores'  => $scores,
            'filters' => $request->only(['month', 'year']),
        ]);
    }

    // ----------------------------------------------------------
    // GET /employee/performance-scores/{id}
    // ----------------------------------------------------------
    public function show(int $id): View
    {
        $score = PerformanceScore::where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('pages.employee.performance_scores.show', ['score' => $score]);
    }

    // ----------------------------------------------------------
    // GET /employee/performance-scores/leaderboard?month=&year=
    // ----------------------------------------------------------
    public function leaderboard(Request $request): View
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        $leaderboard = PerformanceScore::with('user:id,name')
            ->where('company_id', Auth::user()->company_id)
            ->where('month', $month)
            ->where('year', $year)
            ->orderByDesc('final_score')
            ->limit(10)
            ->get()
            ->map(function ($item, $index) {
                $item->rank  = $index + 1;
                $item->is_me = $item->user_id === Auth::id();
                return $item;
            });

        $myScore = PerformanceScore::where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::id())
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        $myRank = null;
        if ($myScore) {
            $myRank = PerformanceScore::where('company_id', Auth::user()->company_id)
                ->where('month', $month)
                ->where('year', $year)
                ->where('final_score', '>', $myScore->final_score)
                ->count() + 1;
        }

        return view('pages.employee.performance_scores.leaderboards', [
            'leaderboard' => $leaderboard,
            'myScore'     => $myScore,
            'myRank'      => $myRank,
            'month'       => $month,
            'year'        => $year,
        ]);
    }
}