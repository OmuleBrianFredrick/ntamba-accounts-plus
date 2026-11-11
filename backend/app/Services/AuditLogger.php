<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Writes the audit log for every change (plan table: audit_log — user, action,
 * table, record, before, after, reason, device, time). Append-only.
 */
class AuditLogger
{
    public static function log(string $action, Model $model, ?array $before = null, ?string $reason = null): void
    {
        AuditLog::withoutGlobalScopes()->create([
            'company_id' => $model->company_id ?? Auth::user()?->company_id,
            'user_id'    => Auth::id(),
            'action'     => $action,
            'table_name' => $model->getTable(),
            'record_id'  => $model->getKey(),
            'before'     => $before,
            'after'      => $model->only($model->getFillable()),
            'reason'     => $reason,
            'device'     => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
