<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Notifications\ReportResolved;
use App\Notifications\ReportRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage reports']);
    }

    public function index(Request $request)
    {
        $query = Report::with(['reporter', 'reportable', 'moderator'])
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->reason, function ($q, $reason) {
                return $q->where('reason', $reason);
            })
            ->when($request->reporter, function ($q, $reporter) {
                return $q->whereHas('reporter', function ($query) use ($reporter) {
                    $query->where('name', 'like', "%{$reporter}%");
                });
            })
            ->when($request->date_from, function ($q, $date) {
                return $q->whereDate('created_at', '>=', $date);
            });

        $pendingCount = Report::pending()->count();
        $reports = $query->latest()->paginate(20);

        $stats = [
            'total' => Report::count(),
            'pending' => $pendingCount,
            'resolved' => Report::where('status', 'resolved')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'pendingCount', 'stats'));
    }

    public function show(Report $report)
    {
        $report->load(['reporter', 'reportable', 'moderator']);
        return view('admin.reports.show', compact('report'));
    }

    public function resolve(Request $request, Report $report)
    {
        $request->validate([
            'moderator_notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($report, $request) {
            $report->resolve(auth()->id(), $request->moderator_notes);
            
            // Notify the reporter
            $report->reporter->notify(new ReportResolved($report));
            
            // If it's a post/topic report, maybe take additional actions
            if ($report->reportable && method_exists($report->reportable, 'markAsReviewed')) {
                $report->reportable->markAsReviewed();
            }
        });

        return response()->json([
            'success' => true,
            'message' => __('main.report_resolved_success')
        ]);
    }

    public function reject(Request $request, Report $report)
    {
        $request->validate([
            'moderator_notes' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($report, $request) {
            $report->reject(auth()->id(), $request->moderator_notes);
            
            // Notify the reporter
            $report->reporter->notify(new ReportRejected($report));
        });

        return response()->json([
            'success' => true,
            'message' => __('main.report_rejected_success')
        ]);
    }

    public function destroy(Report $report)
    {
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => __('main.report_deleted_success')
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:resolve,reject,delete',
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:reports,id'
        ]);

        $count = 0;
        $reports = Report::whereIn('id', $request->report_ids)->get();

        DB::transaction(function () use ($reports, $request, &$count) {
            foreach ($reports as $report) {
                switch ($request->action) {
                    case 'resolve':
                        $report->resolve(auth()->id());
                        $report->reporter->notify(new ReportResolved($report));
                        break;
                    case 'reject':
                        $report->reject(auth()->id());
                        $report->reporter->notify(new ReportRejected($report));
                        break;
                    case 'delete':
                        $report->delete();
                        break;
                }
                $count++;
            }
        });

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => __('main.bulk_action_success', ['count' => $count])
        ]);
    }

    public function resolveAll()
    {
        $count = 0;
        $pendingReports = Report::pending()->get();

        DB::transaction(function () use ($pendingReports, &$count) {
            foreach ($pendingReports as $report) {
                $report->resolve(auth()->id());
                $report->reporter->notify(new ReportResolved($report));
                $count++;
            }
        });

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => __('main.all_reports_resolved', ['count' => $count])
        ]);
    }
} 