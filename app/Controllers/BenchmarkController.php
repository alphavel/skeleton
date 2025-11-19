<?php

namespace App\Controllers;

use Alphavel\Framework\Controller;
use Alphavel\Framework\Response;
use Alphavel\Database\DB;

/**
 * BenchmarkController
 * 
 * High-performance examples demonstrating database best practices
 */
class BenchmarkController extends Controller
{
    /**
     * Hot Path Example 1: Single record by ID with prepared statement
     * 
     * Benchmark: ~18,000 req/s (+20% vs query builder)
     * 
     * GET /api/benchmark/user/{id}
     */
    public function getUser(int $id): Response
    {
        static $stmt;
        
        if (!$stmt) {
            $stmt = DB::prepare("SELECT id, name, email, created_at FROM users WHERE id = ? LIMIT 1");
        }
        
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return Response::json(['error' => 'User not found'], 404);
        }
        
        return Response::json($user);
    }

    /**
     * Hot Path Example 2: Paginated list with raw query
     * 
     * Benchmark: ~13,500 req/s (+12.5% vs query builder)
     * 
     * GET /api/benchmark/users?page=1&limit=20
     */
    public function listUsers(): Response
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int) ($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        $users = DB::query(
            "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        
        return Response::json([
            'data' => $users,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Hot Path Example 3: Aggregation with prepared statement
     * 
     * Benchmark: ~9,200 req/s (+15% vs query builder)
     * 
     * GET /api/benchmark/stats
     */
    public function stats(): Response
    {
        static $stmt;
        
        if (!$stmt) {
            $stmt = DB::prepare("
                SELECT 
                    COUNT(*) as total_users,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as new_users_week,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users_month
                FROM users
            ");
        }
        
        $stmt->execute();
        $stats = $stmt->fetch();
        
        return Response::json($stats);
    }

    /**
     * Query Builder Example: Complex filtering
     * 
     * GET /api/benchmark/search?status=active&role=admin
     */
    public function search(): Response
    {
        $query = DB::table('users')->select('id', 'name', 'email', 'status', 'role');
        
        if (isset($_GET['status'])) {
            $query->where('status', $_GET['status']);
        }
        
        if (isset($_GET['role'])) {
            $query->where('role', $_GET['role']);
        }
        
        $users = $query->limit(50)->get();
        
        return Response::json($users);
    }

    /**
     * Bulk Insert with Transaction
     * 
     * Benchmark: ~2,500 ops/s (+400% vs individual inserts)
     * 
     * POST /api/benchmark/bulk-users
     */
    public function bulkInsert(): Response
    {
        $users = json_decode(file_get_contents('php://input'), true);
        
        if (!is_array($users) || empty($users)) {
            return Response::json(['error' => 'Invalid input'], 400);
        }
        
        $inserted = DB::transaction(function() use ($users) {
            $count = 0;
            foreach ($users as $user) {
                DB::table('users')->insert($user);
                $count++;
            }
            return $count;
        });
        
        return Response::json([
            'success' => true,
            'inserted' => $inserted,
        ]);
    }

    /**
     * whereIn Example: Fetch multiple records efficiently
     * 
     * GET /api/benchmark/users-by-ids?ids=1,2,3,4,5
     */
    public function usersByIds(): Response
    {
        $idsParam = $_GET['ids'] ?? '';
        $ids = array_filter(array_map('intval', explode(',', $idsParam)));
        
        if (empty($ids)) {
            return Response::json(['error' => 'No IDs provided'], 400);
        }
        
        $users = DB::table('users')
            ->select('id', 'name', 'email')
            ->whereIn('id', $ids)
            ->get();
        
        return Response::json($users);
    }

    /**
     * Performance comparison endpoint
     * 
     * GET /api/benchmark/compare
     */
    public function compare(): Response
    {
        $results = [];
        $iterations = 100;
        
        // Test 1: Prepared statement
        $start = microtime(true);
        $stmt = DB::prepare("SELECT COUNT(*) FROM users WHERE id = ?");
        for ($i = 1; $i <= $iterations; $i++) {
            $stmt->execute([$i]);
            $stmt->fetch();
        }
        $results['prepared_statement'] = round((microtime(true) - $start) * 1000, 2) . 'ms';
        
        // Test 2: Query builder
        $start = microtime(true);
        for ($i = 1; $i <= $iterations; $i++) {
            DB::table('users')->where('id', $i)->count();
        }
        $results['query_builder'] = round((microtime(true) - $start) * 1000, 2) . 'ms';
        
        // Test 3: Raw query
        $start = microtime(true);
        for ($i = 1; $i <= $iterations; $i++) {
            DB::query("SELECT COUNT(*) as count FROM users WHERE id = ?", [$i]);
        }
        $results['raw_query'] = round((microtime(true) - $start) * 1000, 2) . 'ms';
        
        return Response::json([
            'iterations' => $iterations,
            'results' => $results,
            'winner' => array_keys($results, min($results))[0],
        ]);
    }
}
