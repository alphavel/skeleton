<?php

namespace App\Controllers;

use Cache;
use DB;
use Log;
use Alphavel\Framework\Controller;
use Alphavel\Framework\Response;

/**
 * ApiController
 *
 * API endpoints with caching and facades
 */
class ApiController extends Controller
{
    /**
     * Get API data (cached)
     */
    public function data(): Response
    {
        // Cache pattern with remember - using auto-generated facade
        $data = Cache::remember('api_data', 300, function () {
            Log::info('Computing expensive API data');

            // Simulate expensive operation
            return [
                'data' => 'Heavy API data computed',
                'timestamp' => now(),
                'random' => rand(1000, 9999),
                'stats' => [
                    'users' => DB::table('users')->count(),
                    'cached_at' => today(),
                ],
            ];
        });

        return Response::success($data);
    }
}
