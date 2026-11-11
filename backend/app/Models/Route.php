<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use BelongsToCompany;

    protected $table = 'routes';

    protected $fillable = ['company_id', 'name', 'zone_type', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

}
