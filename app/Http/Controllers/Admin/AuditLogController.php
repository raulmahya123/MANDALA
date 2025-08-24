<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Tampilkan daftar audit log.
     * Super admin bisa lihat semua, admin dept bisa difilter (opsional).
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filter opsional
        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->integer('user'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where('meta->title', 'like', "%$q%");
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }

    /**
     * Detail audit log tertentu.
     */
    public function show(AuditLog $log)
    {
        return view('admin.audit.show', compact('log'));
    }
}
