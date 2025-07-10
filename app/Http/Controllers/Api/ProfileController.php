<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show(Request $request)
    {
        try {
            $user = $request->user();
            
            $profile = $user->load($user->role === 'driver' ? ['driver.documents', 'driver.ratings'] : []);

            return response()->json([
                'success' => true,
                'data' => $profile
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();
            
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
                'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
                'address' => 'sometimes|string|max:500',
                'city' => 'sometimes|string|max:100',
                'postal_code' => 'sometimes|string|max:10',
                'date_of_birth' => 'sometimes|date|before:today',
                'gender' => 'sometimes|in:male,female,other',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only([
                'name', 'phone', 'address', 'city', 'postal_code', 'date_of_birth', 'gender'
            ]);

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $path = $request->file('avatar')->store('avatars', 'public');
                $updateData['avatar'] = $path;
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update driver profile (for drivers only)
     */
    public function updateDriverProfile(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'vehicle_type' => 'sometimes|in:motorcycle,car,van,truck',
                'vehicle_brand' => 'sometimes|string|max:100',
                'vehicle_model' => 'sometimes|string|max:100',
                'vehicle_year' => 'sometimes|integer|min:1990|max:' . date('Y'),
                'vehicle_plate' => 'sometimes|string|max:20',
                'license_number' => 'sometimes|string|max:50',
                'emergency_contact_name' => 'sometimes|string|max:255',
                'emergency_contact_phone' => 'sometimes|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $driver = $user->driver;
            
            $updateData = $request->only([
                'vehicle_type', 'vehicle_brand', 'vehicle_model', 'vehicle_year',
                'vehicle_plate', 'license_number', 'emergency_contact_name', 'emergency_contact_phone'
            ]);

            $driver->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Driver profile updated successfully',
                'data' => $driver->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update driver profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string',
                'confirmation' => 'required|string|in:DELETE',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password is incorrect'
                ], 400);
            }

            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Delete all tokens
            $user->tokens()->delete();

            // Soft delete user
            $user->update([
                'is_active' => false,
                'deleted_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
