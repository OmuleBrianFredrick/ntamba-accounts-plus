<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Sign-in (plan): agents use phone number + PIN (locked after five wrong
 * tries); accountant/production manager/admin use email + password.
 * Returns a Sanctum bearer token and records the device.
 */
class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone'       => ['sometimes', 'string'],
            'pin'         => ['sometimes', 'digits:4'],
            'email'       => ['sometimes', 'email'],
            'password'    => ['sometimes', 'string'],
            'device_name' => ['required', 'string', 'max:120'],
        ]);

        $user = null;

        if (!empty($data['phone'])) {
            // Agent sign-in with phone + PIN
            $user = User::withoutGlobalScopes()
                ->where('phone', $data['phone'])
                ->where('role', 'agent')
                ->first();

            if (!$user) {
                return response()->json(['message' => 'Invalid credentials.'], 422);
            }

            if ($user->isPinLocked()) {
                return response()->json([
                    'message' => 'PIN locked after 5 wrong attempts. Contact the admin.',
                ], 423);
            }

            if (!$user->verifyPin($data['pin'] ?? '')) {
                $remaining = User::MAX_PIN_ATTEMPTS - (int) $user->pin_failed_attempts;
                return response()->json([
                    'message' => "Wrong PIN. {$remaining} attempt(s) left before lockout.",
                ], 422);
            }
        } elseif (!empty($data['email'])) {
            // Staff sign-in with email + password
            $user = User::withoutGlobalScopes()
                ->where('email', $data['email'])
                ->whereNot('role', 'agent')
                ->first();

            if (!$user || !$user->password || !Hash::check($data['password'] ?? '', $user->password)) {
                return response()->json(['message' => 'Invalid credentials.'], 422);
            }
        } else {
            return response()->json(['message' => 'Provide phone+PIN or email+password.'], 422);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Account disabled.'], 403);
        }

        $token = $user->createToken($data['device_name'])->plainTextToken;

        Device::withoutGlobalScopes()->updateOrCreate(
            ['user_id' => $user->id, 'device_name' => $data['device_name']],
            ['company_id' => $user->company_id, 'last_seen_at' => now(), 'signed_out' => false]
        );

        AuditLogger::log('signed_in', $user);

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->name,
                'role'       => $user->role->value,
                'agent_code' => $user->agent_code,
                'company_id' => $user->company_id,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('company');

        return response()->json($user->only([
            'id', 'name', 'role', 'agent_code', 'phone', 'email',
        ]) + ['company' => $user->company?->only(['id', 'name', 'currency', 'timezone'])]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Signed out.']);
    }
}
