<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filter: action
        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        // Filter: user
        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        // Filter: model
        if ($model = $request->get('model_type')) {
            $query->where('model_type', $model);
        }

        // Filter: tanggal
        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Search: deskripsi
        if ($search = $request->get('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        $logs = $query->paginate(25)->withQueryString();

        // Distinct values untuk filter dropdown
        $actions    = AuditLog::distinct()->pluck('action')->sort()->values();
        $models     = AuditLog::distinct()->whereNotNull('model_type')->pluck('model_type')->sort()->values();
        $users      = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('log-audit', compact('logs', 'actions', 'models', 'users'));
    }
}
