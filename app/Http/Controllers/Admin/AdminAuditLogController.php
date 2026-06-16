<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminAuditLogController extends AdminController
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->orderByDesc('created_at')
            ->when($request->input('q'), function ($q, $search) {
                $q->where('action', 'like', "%{$search}%");
            })
            ->paginate(25)
            ->withQueryString();

        return view('admin.audit.index', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'logs' => $logs,
            'filters' => $request->only(['q']),
        ]);
    }
}
