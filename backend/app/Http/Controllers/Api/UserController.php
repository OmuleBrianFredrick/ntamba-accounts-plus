<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Users & roles (M1). Admin creates users: agent with phone/PIN/agent code,
 * staff with email/password. Nobody deletes — accounts are disabled.
 */
class UserController extends Controller
{
    public function index()
    {
        return User::orderBy('role')->orderBy('name')->get([
            'id', 'name', 'role', 'phone', 'email', 'agent_code', 'is_active', 'created_at',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:120'],
            'role'       => ['required', \Illuminate\Validation\Rules\Enum::class, Role::class],
            'phone'      => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'email'      => ['nullable', 'email', 'max:120', 'unique:users,email'],
            'agent_code' => ['nullable', 'string', 'max:10'],
            'pin'        => ['nullable', 'digits:4', 'required_if:role,agent'],
            'password'   => ['nullable', 'min:8', 'required_if:role,accountant,production_manager,admin'],
        ]);

        $user = new User();
        $user->fill(collect($data)->except(['pin', 'password'])->all());
        if (!empty($data['pin'])) {
            $user->pin_hash = Hash::make($data['pin']); // 'hashed' cast handles it too
        }
        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        AuditLogger::log('created', $user);

        return response()->json($user->only(['id', 'name', 'role', 'phone', 'email', 'agent_code', 'is_active']), 201);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->exists, 404);
        $before = $user->only(['name', 'role', 'phone', 'email', 'agent_code', 'is_active']);

        $data = $request->validate([
            'name'       => ['sometimes', 'string', 'max:120'],
            'phone'      => ['sometimes', 'nullable', 'string', 'max:20', "unique:users,phone,{$user->id}"],
            'email'      => ['sometimes', 'nullable', 'email', 'max:120', "unique:users,email,{$user->id}"],
            'agent_code' => ['sometimes', 'nullable', 'string', 'max:10'],
            'is_active'  => ['sometimes', 'boolean'],
            'pin'        => ['sometimes', 'digits:4'],
            'password'   => ['sometimes', 'min:8'],
            'reason'     => ['required', 'string', 'max:500'], // every change carries a reason
        ]);

        $user->fill(collect($data)->except(['pin', 'password', 'reason'])->all());
        if (!empty($data['pin'])) {
            $user->pin_hash = Hash::make($data['pin']);
            $user->pin_failed_attempts = 0;
            $user->pin_locked_at = null;
        }
        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        AuditLogger::log('updated', $user, $before, $data['reason']);

        return $user->only(['id', 'name', 'role', 'phone', 'email', 'agent_code', 'is_active']);
    }
}
