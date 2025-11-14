<?php

namespace App\Controllers;

use Alphavel\Framework\Controller;
use Alphavel\Framework\Request;
use Alphavel\Framework\Response;
// Cache: usar app("cache") em vez de facade;
// DB: usar app("db") em vez de facade;
// Log: usar app("logger") em vez de facade;
use App\Models\User;

/**
 * UserController
 *
 * Demonstra uso de:
 * - Facades (DB, Cache, Log)
 * - Collections
 * - Helper functions
 * - Type hints
 */
class UserController extends Controller
{
    /**
     * List all users (cached)
     */
    public function index(): Response
    {
        // Cache pattern (Laravel-style)
        $users = Cache::remember('users_list', 300, function () {
            return User::where('status', 'active')
                ->orderBy('name')
                ->get();
        });

        // Collection operations
        $result = collect($users)
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->values()
            ->toArray();

        Log::info('Users listed', ['count' => count($result)]);

        return Response::success($result);
    }

    /**
     * Show single user
     */
    public function show(int $id): Response
    {
        $user = User::find($id);

        if (! $user) {
            Log::warning('User not found', ['id' => $id]);

            return Response::error('User not found', 404);
        }

        return Response::success($user->toArray());
    }

    /**
     * Create new user
     */
    public function store(Request $request): Response
    {
        // Validate input
        $errors = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
        ]);

        if ($errors) {
            return Response::error('Validation failed', 422, $errors);
        }

        // Create user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'status' => 'active',
        ]);

        // Clear cache
        Cache::forget('users_list');

        Log::info('User created', ['id' => $user->id]);

        return Response::success($user->toArray(), 201);
    }

    /**
     * Update user
     */
    public function update(Request $request, int $id): Response
    {
        $user = User::find($id);

        if (! $user) {
            return Response::error('User not found', 404);
        }

        // Update attributes
        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }

        $user->save();

        // Clear cache
        Cache::forget('users_list');

        Log::info('User updated', ['id' => $id]);

        return Response::success($user->toArray());
    }

    /**
     * Delete user
     */
    public function destroy(Request $request, int $id): Response
    {
        $user = User::find($id);

        if (! $user) {
            return Response::error('User not found', 404);
        }

        $user->delete();

        // Clear cache
        Cache::forget('users_list');

        Log::info('User deleted', ['id' => $id]);

        return Response::success(['message' => 'User deleted successfully']);
    }
}
