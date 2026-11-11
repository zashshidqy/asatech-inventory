<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->get('action');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');
        $userId = $request->get('user_id');

        $query = ActivityLog::with('user')
            ->latest('created_at');

        if ($action) {
            $query->where('action', $action);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $logs = $query->paginate(25)->withQueryString();

        // Get distinct actions for filter dropdown
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();

        // Get users for filter dropdown
        $users = \App\Models\User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('activity-logs.index', compact('logs', 'actions', 'users', 'action', 'dateFrom', 'dateTo', 'search', 'userId'));
    }
}