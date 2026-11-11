<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role gate. Usage: ->middleware('role:accountant,admin')
 * Roles are checked on every request (plan: Security).
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role->value, $roles, true)) {
            return response()->json(['message' => 'Forbidden for your role.'], 403);
        }

        return $next($request);
    }
}
