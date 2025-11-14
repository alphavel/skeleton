<?php

namespace App\Controllers;

use DB;
use Alphavel\Framework\Controller;
use Alphavel\Framework\Response;

/**
 * HomeController
 *
 * Main application controller
 */
class HomeController extends Controller
{
    /**
     * Home page / API info
     */
    public function index(): Response
    {
        // Using DB facade (global class, no import needed)
        $result = DB::query('SELECT 1+1 as result');
        
        return Response::make()->json([
            'result' => $result[0]['result']
        ]);
    }
}
