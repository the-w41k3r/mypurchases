<?php

namespace Azuriom\Plugin\MyPurchases\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyPurchasesController extends Controller
{
    /**
     * Show the admin settings page.
     */
    public function index()
    {
        // Get all settings
        $settings = DB::table('mypurchases_settings')->get()->pluck('value', 'name');

        // Pass the settings to the view
        return view('mypurchases::admin.index', [
            'isEnabled' => $settings['is_purchases_page_enabled'] ?? '1',
            'tebexSecret' => $settings['tebex_secret'] ?? '',
            'tebexStoreUrl' => $settings['tebex_store_url'] ?? ''
        ]);
    }

    /**
     * Save the settings from the admin form.
     */
    public function save(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'is_purchases_page_enabled' => 'required|in:1,0',
            'tebex_secret' => 'nullable|string',
            'tebex_store_url' => 'nullable|url'
        ]);

        // Update all settings
        foreach ($validated as $name => $value) {
            DB::table('mypurchases_settings')
                ->updateOrInsert(
                    ['name' => $name],
                    ['value' => $value, 'updated_at' => now()]
                );
        }

        return redirect()->route('mypurchases.admin.index')
                         ->with('success', 'Settings saved successfully!');
    }


}
