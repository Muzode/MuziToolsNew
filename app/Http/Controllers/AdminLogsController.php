<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminLogsController extends Controller
{
    /**
     * Menampilkan daftar activity logs dengan search dan filter
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Search: nama user, action, atau description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('action', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by Action
        if ($request->has('action') && $request->action) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        // Sorting
        $sort = $request->get('sort', 'date'); // Default sort by date
        $order = $request->get('order', 'desc'); // Default order desc

        switch ($sort) {
            case 'user':
                $query->join('users', 'activity_logs.user_id', '=', 'users.id')
                      ->orderBy('users.name', $order)
                      ->select('activity_logs.*');
                break;
            case 'action':
                $query->orderBy('action', $order);
                break;
            case 'no':
                $query->orderBy('id', $order);
                break;
            case 'date':
            default:
                $query->orderBy('created_at', $order);
                break;
        }

        // Pagination
        $logs = $query->paginate(20);

        return view('admin.logs', compact('logs'));
    }
}