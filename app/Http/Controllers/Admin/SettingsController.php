<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // Show the settings page
    public function index()
    {
        // Load all settings into a key => value collection
        // e.g. $settings['shop_name'] = 'BrewTrack Coffee Shop'
        $settings = Setting::all()->pluck('setting_value', 'setting_key');

        return view('admin.settings.index', compact('settings'));
    }

    // Save settings
    public function update(Request $request)
    {
        $validated = $request->validate([
    'shop_name'          => 'required|string|max:100',
    'shop_address'       => 'nullable|string|max:255',
    'shop_contact'       => 'nullable|string|max:50',
    'shop_email'         => 'nullable|email|max:100',
    'tax_rate'           => 'required|numeric|min:0|max:1',
    'currency'           => 'required|string|max:10',
    'receipt_header'     => 'nullable|string|max:255',
    'receipt_footer'     => 'nullable|string|max:255',
    'printer_name'       => 'nullable|string|max:100',
    'printer_connection' => 'nullable|in:windows,network',
    'printer_ip'         => 'nullable|string|max:50',
    'printer_port'       => 'nullable|numeric',
    'auto_print'         => 'boolean',
]);

// Checkbox returns null when unchecked — convert to string '0' or '1'
$validated['auto_print'] = $request->has('auto_print') ? '1' : '0';
        // Save each setting using the Setting model's set() helper
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['setting_key'   => $key],
                ['setting_value' => $value]
            );
        }

        return back()->with('success', 'Settings saved successfully!');
    }
}