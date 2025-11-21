<?php

namespace App\Controllers;

use Alphavel\Framework\Controller;
use Alphavel\Framework\Request;
use Alphavel\Framework\Response;

class ExampleController extends Controller
{
    /**
     * List all resources
     */
    public function index()
    {
        // Example: Fetch from database or service
        $items = [
            ['id' => 1, 'name' => 'Item 1', 'status' => 'active'],
            ['id' => 2, 'name' => 'Item 2', 'status' => 'active'],
        ];

        return Response::make()->json([
            'data' => $items,
            'total' => count($items)
        ]);
    }

    /**
     * Show single resource
     */
    public function show(int $id)
    {
        // Example: Fetch from database or service
        $item = [
            'id' => $id,
            'name' => "Item {$id}",
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        return Response::make()->json(['data' => $item]);
    }

    /**
     * Create new resource
     */
    public function store(Request $request)
    {
        $data = $request->all();
        
        // Example: Validate and save to database
        // $validator = Validator::make($data, [
        //     'name' => 'required|string|max:255',
        // ]);
        
        return Response::make()->json([
            'message' => 'Resource created successfully',
            'data' => array_merge(['id' => rand(1000, 9999)], $data)
        ], 201);
    }

    /**
     * Update existing resource
     */
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        
        // Example: Validate and update in database
        
        return Response::make()->json([
            'message' => 'Resource updated successfully',
            'data' => array_merge(['id' => $id], $data)
        ]);
    }

    /**
     * Delete resource
     */
    public function destroy(int $id)
    {
        // Example: Delete from database
        
        return Response::make()->json([
            'message' => 'Resource deleted successfully'
        ]);
    }
}
