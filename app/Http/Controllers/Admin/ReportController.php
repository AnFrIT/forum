<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Report::with(['reportable', 'reporter', 'moderator']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        if ($request->filled('reporter')) {
            $query->whereHas('reporter', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->reporter . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('type')) {
            $query->where('reportable_type', 'App\Models\\'.ucfirst($request->type));
        }

        $reports = $query->latest()->paginate(20);

        // ДОБАВЬТЕ ЭТИ ПЕРЕМЕННЫЕ:
        $stats = [
            'pending' => Report::where('status', 'pending')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
            'total' => Report::count(),
        ];

        $pendingCount = $stats['pending'];

        // Prepare reason labels
        $reasonLabels = [
            'spam' => 'Спам',
            'inappropriate_content' => 'Неприемлемый контент',
            'harassment' => 'Домогательство',
            'fake_information' => 'Ложная информация',
            'copyright' => 'Нарушение авторских прав',
            'other' => 'Другое',
        ];

        return view('admin.reports.index', compact('reports', 'stats', 'pendingCount', 'reasonLabels'));
    }

    public function show(Report $report)
    {
        $this->authorize('manage reports');
        
        return view('admin.reports.show', compact('report'));
    }

    public function resolve(Request $request, Report $report)
    {
        $this->authorize('manage reports');

        $validated = $request->validate([
            'moderator_notes' => 'nullable|string|max:500',
        ]);

        $report->resolve(auth()->id(), $validated['moderator_notes'] ?? null);

        return back()->with('success', 'Жалоба помечена как решенная.');
    }

    public function reject(Request $request, Report $report)
    {
        $this->authorize('manage reports');

        $validated = $request->validate([
            'moderator_notes' => 'nullable|string|max:500',
        ]);

        $report->reject(auth()->id(), $validated['moderator_notes'] ?? null);

        return back()->with('success', 'Жалоба отклонена.');
    }

    public function resolveAll()
    {
        $this->authorize('manage reports');
        
        $pendingReports = Report::where('status', 'pending')->get();
        $count = $pendingReports->count();
        
        foreach ($pendingReports as $report) {
            $report->resolve(auth()->id(), 'Массовое решение жалоб');
        }
        
        return back()->with('success', "Решено $count жалоб.");
    }

    public function destroy(Report $report)
    {
        $this->authorize('manage reports');
        
        $report->delete();
        
        return back()->with('success', 'Жалоба удалена.');
    }
}
