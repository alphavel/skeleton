<?php

/**
 * Alphavel Database Configuration
 * 
 * ⚡ This configuration is optimized for maximum performance out-of-the-box.
 * 
 * Key Optimizations:
 * - ATTR_EMULATE_PREPARES => false (+20% performance with Global Statement Cache)
 * - No ATTR_PERSISTENT (redundant in Swoole, prevents lock contention)
 * - No pool_size by default (singleton connectionRead() is faster for reads)
 * 
 * 📚 Learn more: https://github.com/alphavel/database#performance-tuning
 */

use Alphavel\Database\DB;

return [
    'database' => [
        'default' => 'mysql',
        
        'connections' => [
            /**
             * MySQL Connection (Optimized)
             * 
             * This is the recommended configuration for 99% of applications.
             * Achieves 7,000+ req/s on database-heavy workloads.
             */
            'mysql' => DB::optimizedConfig([
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', 3306),
                'database' => env('DB_DATABASE', 'alphavel'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
            ]),
            
            /**
             * PostgreSQL Connection (Optimized)
             * 
             * Same optimizations apply to PostgreSQL.
             * Uncomment and configure as needed.
             */
            // 'pgsql' => DB::optimizedConfig([
            //     'driver' => 'pgsql',
            //     'host' => env('DB_HOST', '127.0.0.1'),
            //     'port' => env('DB_PORT', 5432),
            //     'database' => env('DB_DATABASE', 'alphavel'),
            //     'username' => env('DB_USERNAME', 'postgres'),
            //     'password' => env('DB_PASSWORD', ''),
            //     'charset' => 'utf8',
            // ]),
            
            /**
             * SQLite Connection (Optimized)
             * 
             * Perfect for testing and small applications.
             */
            // 'sqlite' => DB::optimizedConfig([
            //     'driver' => 'sqlite',
            //     'database' => env('DB_DATABASE', storage_path('database.sqlite')),
            // ]),
        ],
    ],
];
