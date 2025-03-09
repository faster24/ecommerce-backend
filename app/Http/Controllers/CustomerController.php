<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Get the authenticated customer's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(Request $request)
    {
        $customer = $request->user('sanctum');

        if (!$customer) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'customer' => $customer,
        ]);
    }

    /**
     * Update the authenticated customer's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $customer = $request->user('sanctum');

        if (!$customer) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:shop_customers,email,' . $customer->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'photo' => 'sometimes|string|nullable',
            'gender' => 'sometimes|in:male,female',
            'phone' => 'sometimes|string|nullable',
            'city' => 'sometimes|string|nullable',
            'state' => 'sometimes|string|nullable',
            'birthday' => 'sometimes|date|nullable',
        ]);

        $data = $request->only([
            'name',
            'email',
            'photo',
            'gender',
            'phone',
            'city',
            'state',
            'birthday',
        ]);

        // If password is provided, hash it
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'customer' => $customer,
        ]);
    }
}
