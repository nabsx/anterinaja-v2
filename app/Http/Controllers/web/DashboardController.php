<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\FareCalculationService;
use App\Services\OSRMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $fareService;
    protected $osrmService;

    public function __construct(FareCalculationService $fareService, OSRMService $osrmService)
    {
        $this->fareService = $fareService;
        $this->osrmService = $osrmService;
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'driver') {
            return redirect()->route('driver.dashboard');
        } elseif ($user->role === 'customer') {
            return redirect()->route('customer.dashboard');
        }

        // Corrected view path
        return view('dashboard.index', ['user' => $user]);
    }

    public function calculateFare(Request $request)
    {
        $request->validate([
            'pickup_address' => 'required|string',
            'destination_address' => 'required|string',
            'service_type' => 'in:motorcycle,car',
        ]);

        try {
            // Get coordinates from addresses (you might need to implement geocoding)
            $pickupCoords = $this->geocodeAddress($request->pickup_address);
            $destinationCoords = $this->geocodeAddress($request->destination_address);

            if (!$pickupCoords || !$destinationCoords) {
                return back()->with('error', 'Alamat tidak ditemukan. Silakan gunakan alamat yang lebih spesifik.');
            }

            $serviceType = $request->service_type ?? 'motorcycle';

            $fareData = $this->fareService->calculate(
                $pickupCoords['lat'],
                $pickupCoords['lng'],
                $destinationCoords['lat'],
                $destinationCoords['lng'],
                $serviceType
            );

            return back()->with('fare_result', $fareData);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghitung ongkir. Silakan coba lagi.');
        }
    }

    private function geocodeAddress($address)
    {
        // Simple geocoding simulation - in real app, use Google Maps or other geocoding service
        // For now, return sample coordinates
        return [
            'lat' => -6.2088 + (rand(-100, 100) / 1000),
            'lng' => 106.8456 + (rand(-100, 100) / 1000)
        ];
    }
}