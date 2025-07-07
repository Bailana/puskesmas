<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->where(function ($query) {
                $query->where('action', 'like', '%login%')
                      ->orWhere('action', 'like', '%tambah%')
                      ->orWhere('action', 'like', '%ubah%')
                      ->orWhere('action', 'like', '%hapus%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.log', compact('logs'));
    }
}
