<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * audit_log rows. Not company-scoped for reads by admins/accountants through
 * their own queries; writes go through AuditLogger.
 */
class AuditLog extends Model
{
    use BelongsToCompany;

    protected $table = 'audit_logs';
    public $timestamps = false;

    protected $fillable = [
        'company_id', 'user_id', 'action', 'table_name', 'record_id',
        'before', 'after', 'reason', 'device', 'created_at',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
