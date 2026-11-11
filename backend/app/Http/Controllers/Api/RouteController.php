<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

/**
 * Routes (M1): Kampala / Eastern Route / Western Route, each marked
 * kampala or upcountry. Admin manages; deactivate, never delete.
 */
class RouteController extends Controller
{
    public function index()
    {
        return Route::orderBy('name')->get(['id', 'name', 'zone_type', 'is_active']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:80'],
            'zone_type' => ['required', 'in:kampala,upcountry'],
        ]);

        $route = Route::create($data);
        AuditLogger::log('created', $route);

        return response()->json($route->only(['id', 'name', 'zone_type', 'is_active']), 201);
    }

    public function update(Request $request, Route $route)
    {
        abort_unless($route->exists, 404);
        $before = $route->only(['name', 'zone_type', 'is_active']);

        $data = $request->validate([
            'name'      => ['sometimes', 'string', 'max:80'],
            'zone_type' => ['sometimes', 'in:kampala,upcountry'],
            'is_active' => ['sometimes', 'boolean'],
            'reason'    => ['required', 'string', 'max:500'],
        ]);

        $reason = $data['reason'];
        unset($data['reason']);

        $route->fill($data)->save();
        AuditLogger::log('updated', $route, $before, $reason);

        return $route->only(['id', 'name', 'zone_type', 'is_active']);
    }
}
