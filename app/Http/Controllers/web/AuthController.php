<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

            if ($user->role === 'driver') {
                return redirect()->route('driver.dashboard');
            } elseif ($user->role === 'customer') {
                return redirect()->route('customer.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->withInput();
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:customer,driver',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'vehicle_type' => 'nullable|required_if:role,driver|in:motorcycle,car',
            'vehicle_brand' => 'nullable|required_if:role,driver|string|max:100',
            'vehicle_model' => 'nullable|required_if:role,driver|string|max:100',
            'vehicle_year' => 'nullable|required_if:role,driver|integer|min:1990|max:' . date('Y'),
            'vehicle_plate' => 'nullable|required_if:role,driver|string|max:20',
            'license_number' => 'nullable|required_if:role,driver|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

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
        ]);

        // Create driver profile if role is driver
        if ($request->role === 'driver') {
            Driver::create([
                'user_id' => $user->id,
                'vehicle_type' => $request->vehicle_type ?? 'motorcycle',
                'vehicle_plate' => $request->vehicle_plate ?? 'TEMP-' . $user->id,
                'license_number' => $request->license_number ?? 'TEMP-' . $user->id,
                'is_verified' => false,
                'is_online' => false,
                'status' => 'offline',
                'rating' => 5.00,
                'total_trips' => 0,
                'balance' => 0.00,
            ]);
        }

        Auth::login($user);

        if ($user->role === 'driver') {
            return redirect()->route('driver.dashboard')->with('success', 'Akun driver berhasil dibuat! Silakan lengkapi dokumen untuk verifikasi.');
        } else {
            return redirect()->route('customer.dashboard')->with('success', 'Akun customer berhasil dibuat!');
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