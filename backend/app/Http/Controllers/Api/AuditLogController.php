<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Device;
use Illuminate\Http\Request;

/**
 * Audit log reader (accountant + admin only) and device list with remote
 * sign-out (a lost phone is signed out from the console).
 */
class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        return AuditLog::query()
            ->with('user:id,name,role')
            ->when($request->table, fn ($q, $t) => $q->where('table_name', $t))
            ->when($request->user_id, fn ($q, $u) => $q->where('user_id', $u))
            ->latest('id')
            ->paginate(50);
    }
}
