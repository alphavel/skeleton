<?php

namespace App\Controllers;

use Alphavel\Framework\Controller;
use Alphavel\Framework\Request;
use Alphavel\Framework\Response;
use App\Models\User;

/**
 * AuthController
 *
 * Authentication endpoints with rate limiting
 */
class AuthController extends Controller
{
    /**
     * Login endpoint
     */
    public function login(Request $request): Response
    {
        // Validate request
        $rules = [
            'username' => 'required',
            'password' => 'required|min:4',
        ];

        $validated = $request->validate($rules);
        if (! $validated['valid']) {
            Log::warning('Login failed: validation error', $validated['errors']);

            return Response::error('Validation failed', 422, $validated['errors']);
        }

        $data = $validated['data'];

        // Rate limiting with cache
        $rateLimitKey = "rate_limit:login:{$data['username']}";
        $attempts = Cache::get($rateLimitKey) ?? 0;

        if ($attempts >= 5) {
            Log::warning('Login rate limit exceeded', ['username' => $data['username']]);

            return Response::error('Too many attempts. Try again later.', 429);
        }

        // Simulate authentication (in production, check DB)
        $user = User::where('username', $data['username'])->first();

        if ($user && $data['password'] === 'pass') {
            // Clear rate limit on success
            Cache::forget($rateLimitKey);

            // Generate fake token (in production, use JWT)
            $token = base64_encode($data['username'].':'.time());

            Log::info('User logged in', ['username' => $data['username']]);

            return Response::success([
                'token' => $token,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id ?? 1,
                    'username' => $data['username'],
                ],
            ]);
        }

        // Increment rate limit
        Cache::set($rateLimitKey, $attempts + 1, 300); // 5 minutes

        Log::warning('Login failed: invalid credentials', ['username' => $data['username']]);

        return Response::error('Invalid credentials', 401);
    }

    /**
     * Register endpoint
     */
    public function register(Request $request): Response
    {
        // Validate request
        $rules = [
            'username' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];

        $validated = $request->validate($rules);
        if (! $validated['valid']) {
            Log::warning('Register failed: validation error', $validated['errors']);

            return Response::error('Validation failed', 422, $validated['errors']);
        }

        $data = $validated['data'];

        // Check if user exists
        $exists = User::where('username', $data['username'])->first();
        if ($exists) {
            return Response::error('Username already taken', 409);
        }

        // Create user (simulate)
        $user = [
            'id' => rand(1000, 9999),
            'username' => $data['username'],
            'email' => $data['email'],
            'created_at' => now(),
        ];

        Log::info('User registered', ['username' => $data['username']]);

        return Response::success([
            'message' => 'Registration successful',
            'user' => $user,
        ], 201);
    }
}
