<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PriceHistory;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

/**
 * Products and prices (M1). Price changes write price_history so old sales
 * keep the price they were made at. Agents never see unit_cost.
 */
class ProductController extends Controller
{
    private function visibleFields(): array
    {
        $fields = [
            'id', 'code', 'name', 'size', 'category', 'tracks_expiry',
            'unit_price', 'is_active', 'external_id',
        ];
        // Unit costs: accountant, production manager and admin only (plan table).
        if (in_array(auth()->user()->role->value, ['accountant', 'production_manager', 'admin'], true)) {
            $fields[] = 'unit_cost';
        }

        return $fields;
    }

    public function index(Request $request)
    {
        return Product::query()
            ->when($request->q, fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->orderBy('category')->orderBy('name')
            ->get($this->visibleFields());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'          => ['nullable', 'string', 'max:30'],
            'name'          => ['required', 'string', 'max:120'],
            'size'          => ['nullable', 'string', 'max:30'],
            'category'      => ['nullable', 'string', 'max:60'],
            'tracks_expiry' => ['boolean'],
            'unit_price'    => ['required', 'integer', 'min:0'], // whole UGX
            'unit_cost'     => ['nullable', 'integer', 'min:0'],
            'external_id'   => ['nullable', 'string', 'max:60'],
        ]);

        $product = Product::create($data);
        PriceHistory::create([
            'product_id'     => $product->id,
            'price'          => $product->unit_price,
            'effective_from' => now()->toDateString(),
        ]);

        AuditLogger::log('created', $product);

        return response()->json($product->only($this->visibleFields()), 201);
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->exists, 404);
        $before = $product->only($this->visibleFields());

        $data = $request->validate([
            'code'          => ['sometimes', 'nullable', 'string', 'max:30'],
            'name'          => ['sometimes', 'string', 'max:120'],
            'size'          => ['sometimes', 'nullable', 'string', 'max:30'],
            'category'      => ['sometimes', 'nullable', 'string', 'max:60'],
            'tracks_expiry' => ['sometimes', 'boolean'],
            'unit_price'    => ['sometimes', 'integer', 'min:0'],
            'unit_cost'     => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_active'     => ['sometimes', 'boolean'],
            'external_id'   => ['sometimes', 'nullable', 'string', 'max:60'],
            'reason'        => ['required', 'string', 'max:500'],
        ]);

        $reason = $data['reason'];
        unset($data['reason']);

        $priceChanged = isset($data['unit_price']) && (int) $data['unit_price'] !== $product->unit_price;
        $product->fill($data)->save();

        if ($priceChanged) {
            PriceHistory::create([
                'product_id'     => $product->id,
                'price'          => $product->unit_price,
                'effective_from' => now()->toDateString(),
            ]);
        }

        AuditLogger::log('updated', $product, $before, $reason);

        return $product->only($this->visibleFields());
    }
}
