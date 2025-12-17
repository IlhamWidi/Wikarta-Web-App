<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            // Assign default role with fallback
            $defaultRole = Role::where('name', 'marketing')->first();
            if ($defaultRole) {
                $user->assignRole('marketing');
            } else {
                Log::warning('Default role "marketing" not found for user registration', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            
            DB::commit();

            return response()->json([
                'user' => $this->formatUser($user->fresh()),
                'token' => $token,
                'message' => 'Registrasi berhasil'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);
            throw ValidationException::withMessages([
                'email' => ['Registrasi gagal. Silakan coba lagi.'],
            ]);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda tidak aktif.'],
            ]);
        }

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $this->formatUser($user->fresh()),
            'token' => $token,
            'message' => 'Login berhasil'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $this->formatUser($request->user()->fresh())
        ]);
    }

    private function formatUser(User $user): array
    {
        $user->loadMissing('roles');
        
        $primaryRole = null;
        if ($user->roles && $user->roles->isNotEmpty()) {
            $primaryRole = $user->roles->first()->name;
        }

        $permissions = [];
        try {
            $permissions = $user->getAllPermissions()->pluck('name')->values();
        } catch (\Exception $e) {
            Log::warning('Failed to load user permissions', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        return array_merge($user->toArray(), [
            'role' => $primaryRole ? Str::headline($primaryRole) : 'User',
            'permissions' => $permissions,
        ]);
    }
}
