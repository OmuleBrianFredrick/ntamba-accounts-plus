<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

/**
 * Company settings (M1): payment methods + required evidence, alert
 * thresholds, route target default, labels, logo placeholder. Everything
 * configurable per company — no business name hard-coded.
 */
class SettingController extends Controller
{
    /** Defaults from the plan's setup steps and Alerts section. */
    public const DEFAULTS = [
        'payment_methods' => [
            ['key' => 'cash',  'label' => 'Cash',         'evidence' => 'photo_receipt'],
            ['key' => 'momo',  'label' => 'MTN MoMo',     'evidence' => 'transaction_id_screenshot'],
            ['key' => 'airtel','label' => 'Airtel Money', 'evidence' => 'transaction_id_screenshot'],
            ['key' => 'bank',  'label' => 'Bank transfer','evidence' => 'reference_slip_photo'],
        ],
        'route_target_default' => 100000, // UGX per day, motivation only
        'alert_thresholds' => [
            'day_close_time'      => '19:00',
            'no_sales_by_time'    => '14:00',
            'stock_variance_pct'  => 5,
            'credit_limit'        => 0,
            'overdue_days'        => 30,
        ],
        'categories' => [
            'trip_expenses'   => ['Allowances', 'Car repair', 'Fuel', 'Street parking', 'Offloading'],
            'office_expenses' => ['Lunch', 'Transport', 'Resources', 'Rent', 'Salaries', 'AOB'],
            'income_types'    => ['AOB'],
        ],
    ];

    public function index()
    {
        $stored = Setting::pluck('value', 'key')->toArray();

        return array_merge(self::DEFAULTS, $stored);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'key'   => ['required', 'string', 'max:60'],
            'value' => ['required'],
            'reason'=> ['required', 'string', 'max:500'],
        ]);

        $setting = Setting::firstOrNew(['key' => $data['key']]);
        $before  = $setting->exists ? $setting->only(['value']) : null;
        $setting->value = $data['value'];
        $setting->save();

        AuditLogger::log($before ? 'updated' : 'created', $setting, $before, $data['reason']);

        return ['key' => $setting->key, 'value' => $setting->value];
    }
}
