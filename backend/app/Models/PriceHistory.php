<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use BelongsToCompany;

    protected $table = 'price_histories';

    protected $fillable = ['company_id', 'product_id', 'price', 'effective_from'];

    protected $casts = ['effective_from' => 'date', 'price' => 'integer'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
