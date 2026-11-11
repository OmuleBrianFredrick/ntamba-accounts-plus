<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'code', 'name', 'size', 'category', 'tracks_expiry',
        'unit_price', 'unit_cost', 'is_active', 'external_id',
    ];

    protected $casts = [
        'tracks_expiry' => 'boolean',
        'is_active'     => 'boolean',
        'unit_price'    => 'integer',
        'unit_cost'     => 'integer',
    ];

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class);
    }
}
