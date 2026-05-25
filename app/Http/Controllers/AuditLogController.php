<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()
            ->with('user')
            ->when($request->filled('action'), function ($query) use ($request) {
                $query->where('action', $request->string('action')->toString());
            })
            ->when($request->filled('table_name'), function ($query) use ($request) {
                $query->where('table_name', $request->string('table_name')->toString());
            })
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->integer('user_id'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date('date_to'));
            });

        $summaryQuery = clone $query;

        $auditLogs = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'today' => (clone $summaryQuery)->whereDate('created_at', today())->count(),

            'total_logs' => (clone $summaryQuery)->count(),
            'logs_today' => (clone $summaryQuery)->whereDate('created_at', today())->count(),
            'today_logs' => (clone $summaryQuery)->whereDate('created_at', today())->count(),

            'creates_today' => (clone $summaryQuery)
                ->whereDate('created_at', today())
                ->whereIn('action', ['created', 'create', 'store'])
                ->count(),

            'updates_today' => (clone $summaryQuery)
                ->whereDate('created_at', today())
                ->whereIn('action', ['updated', 'update'])
                ->count(),

            'deletes_today' => (clone $summaryQuery)
                ->whereDate('created_at', today())
                ->whereIn('action', ['deleted', 'delete', 'destroy'])
                ->count(),

            'system_today' => (clone $summaryQuery)
                ->whereDate('created_at', today())
                ->whereNull('user_id')
                ->count(),
        ];

        $actions = AuditLog::query()
            ->select('action')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $tables = AuditLog::query()
            ->select('table_name')
            ->whereNotNull('table_name')
            ->distinct()
            ->orderBy('table_name')
            ->pluck('table_name');

        return view('audit-logs.index', [
            'auditLogs' => $auditLogs,
            'logs' => $auditLogs,
            'summary' => $summary,
            'actions' => $actions,
            'tables' => $tables,
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user');

        return view('audit-logs.show', [
            'auditLog' => $auditLog,
        ]);
    }
}