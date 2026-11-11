<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'currency', 'timezone', 'invoice_prefix', 'settings', 'is_training',
    ];

    protected $casts = [
        'settings'    => 'array',
        'is_training' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /** A configurable setting with a default (logo, thresholds, labels...). */
    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
