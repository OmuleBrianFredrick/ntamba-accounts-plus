<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\AuditLogger;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;

/**
 * Device list with remote sign-out (a lost phone is signed out from the
 * console; the app clears its local data on next open).
 */
class DeviceController extends Controller
{
    public function index()
    {
        return Device::with('user:id,name,role')->latest('last_seen_at')->get();
    }

    /** Remote sign-out: revoke the token and flag the device; the app then
     *  clears its local data on next open. */
    public function signOut(Device $device)
    {
        abort_unless($device->exists, 404);

        $device->update(['signed_out' => true]);
        \Laravel\Sanctum\PersonalAccessToken::where('id', $device->token_id)
            ->where('tokenable_type', \App\Models\User::class)
            ->delete();

        AuditLogger::log('device_signed_out', $device, null, 'Remote sign-out from console');

        return ['message' => 'Device signed out.'];
    }
}
