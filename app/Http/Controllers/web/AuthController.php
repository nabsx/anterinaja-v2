<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'driver' => redirect()->route('driver.dashboard'),
                'customer' => redirect()->route('customer.dashboard'),
                default => abort(403),
            };
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'driver' => redirect()->route('driver.dashboard'),
                'customer' => redirect()->route('customer.dashboard'),
                default => abort(403),
            };
        }
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->updateLastLogin();

            // Redirect berdasarkan role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'driver' => redirect()->route('driver.dashboard'),
                'customer' => redirect()->route('customer.dashboard'),
                default => redirect()->route('home')->with('error', 'Role tidak valid.'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->withInput();
    }

    public function register(Request $request)
    {
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:customer,driver',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
        ];

        // Add driver-specific validation rules if role is driver
        if ($request->role === 'driver') {
            $rules = array_merge($rules, [
                'vehicle_type' => 'required|in:motorcycle,car,van,truck',
                'vehicle_brand' => 'required|string|max:100',
                'vehicle_model' => 'required|string|max:100',
                'vehicle_year' => 'required|integer|min:1990|max:' . date('Y'),
                'vehicle_plate' => 'required|string|max:20|unique:drivers,vehicle_plate',
                'license_number' => 'required|string|max:50|unique:drivers,license_number',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'address' => $request->address,
                'city' => $request->city,
                'is_active' => true,
                'email_verified_at' => now(), // Auto verify for web registration
            ]);

            // Create driver profile if role is driver
            if ($request->role === 'driver') {
                Driver::create([
                    'user_id' => $user->id,
                    'vehicle_type' => $request->vehicle_type,
                    'vehicle_brand' => $request->vehicle_brand,
                    'vehicle_model' => $request->vehicle_model,
                    'vehicle_year' => $request->vehicle_year,
                    'vehicle_plate' => strtoupper($request->vehicle_plate),
                    'license_number' => $request->license_number,
                    'is_verified' => false,
                    'is_online' => false,
                    'status' => 'offline',
                    'rating' => 5.00,
                    'total_trips' => 0,
                    'balance' => 0.00,
                ]);
            }

            DB::commit();

            // Auto login the user
            Auth::login($user);

            // Redirect based on role with success message
            if ($user->role === 'driver') {
                return redirect()->route('driver.dashboard')->with('success', 'Akun driver berhasil dibuat! Silakan lengkapi dokumen untuk verifikasi.');
            } else {
                return redirect()->route('customer.dashboard')->with('success', 'Akun customer berhasil dibuat! Selamat datang di AnterInAja.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors([
                'general' => 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.'
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Berhasil logout.');
    }
}
