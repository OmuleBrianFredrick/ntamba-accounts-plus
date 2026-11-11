<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'user_id', 'device_name', 'token_id',
        'last_seen_at', 'signed_out',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'signed_out'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
