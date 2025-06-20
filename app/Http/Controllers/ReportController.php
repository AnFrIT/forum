<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Report;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        // Check if user can create reports
        if (Gate::denies('create', Report::class)) {
            return back()->with('error', __('main.cannot_create_reports'));
        }

        $validated = $request->validate([
            'reportable_type' => 'required|in:topic,post',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string|in:spam,offensive,offtopic,rules,other',
            'description' => 'required|string|max:500',
        ]);

        // Get the reportable model
        if ($validated['reportable_type'] === 'topic') {
            $reportable = Topic::findOrFail($validated['reportable_id']);
        } else {
            $reportable = Post::findOrFail($validated['reportable_id']);
        }

        // Check if user already reported this
        $existingReport = $reportable->reports()
            ->where('reporter_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existingReport) {
            return back()->with('error', __('main.already_reported'));
        }

        // Create report
        $report = $reportable->reports()->create([
            'reporter_id' => auth()->id(),
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'priority' => $this->determineReportPriority($validated['reason'], $reportable),
        ]);

        // Notify moderators about new report
        $this->notifyModerators($report);

        return back()->with('success', __('main.report_submitted'));
    }

    public function show(Report $report)
    {
        // Check if user can view this report
        if (Gate::denies('view', $report)) {
            abort(403);
        }

        return view('reports.show', compact('report'));
    }

    protected function determineReportPriority($reason, $reportable)
    {
        // Set high priority for serious violations
        if (in_array($reason, ['offensive', 'rules'])) {
            return 'high';
        }

        // Set high priority if reporter is trusted
        if (auth()->user()->isTrusted()) {
            return 'high';
        }

        // Set high priority if reportable author has previous violations
        if ($reportable->user->hasRecentViolations()) {
            return 'high';
        }

        return 'normal';
    }

    protected function notifyModerators($report)
    {
        // This will be implemented when we add the notification system
        // For now, moderators will see new reports in their dashboard
    }
}
