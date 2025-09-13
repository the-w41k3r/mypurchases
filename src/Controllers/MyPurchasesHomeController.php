<?php

namespace Azuriom\Plugin\MyPurchases\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\MyPurchases\Services\TebexService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MyPurchasesHomeController extends Controller
{
    /**
     * Show the home plugin page.
     */
    // public function index()
    // {
    //     return view('mypurchases::index');
    // }

    /**
     * Show the purchases page.
     */
    public function index()
    {
        // Check if the purchases page is enabled
        $setting = DB::table('mypurchases_settings')
                    ->where('name', 'is_purchases_page_enabled')
                    ->first();

        if (!$setting || $setting->value == '0') {
            abort(404);
        }

        // Get Tebex settings
        $tebexSettings = DB::table('mypurchases_settings')
                          ->whereIn('name', ['tebex_secret', 'tebex_store_url'])
                          ->get()
                          ->pluck('value', 'name');

        // Get logged-in user
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view your purchases.');
        }

        // Fetch basic purchases from Tebex (without package details)
        $tebexService = new TebexService(
            $tebexSettings['tebex_secret'] ?? '',
            $tebexSettings['tebex_store_url'] ?? ''
        );

        $ch = curl_init("https://plugin.tebex.io/information");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Tebex-Secret: {$tebexSettings['tebex_secret']}"]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $purchases = $tebexService->getUserPurchases($user->name);

            return view('mypurchases::index', [
                'purchases' => $purchases,
                'user' => $user,
                'debugInfo' => [ // Make sure this array is included
                    'httpCode' => $httpCode,
                    'apiResponse' => $response,
                    'secret' => $tebexSettings['tebex_secret'],
                    'storeUrl' => $tebexSettings['tebex_store_url']
                ]
            ]);

    }

    /**
     * API endpoint to get payment details (lazy loading)
     */
    public function getPaymentDetails(Request $request, $transactionId)
    {
        // Get Tebex settings
        $tebexSettings = DB::table('mypurchases_settings')
                          ->whereIn('name', ['tebex_secret', 'tebex_store_url'])
                          ->get()
                          ->pluck('value', 'name');

        $tebexService = new TebexService(
            $tebexSettings['tebex_secret'] ?? '',
            $tebexSettings['tebex_store_url'] ?? ''
        );

        $paymentDetails = $tebexService->getPaymentDetails($transactionId);

        if (!$paymentDetails) {
            return response()->json(['error' => 'Payment details not found'], 404);
        }

        return response()->json($paymentDetails);
    }
}
