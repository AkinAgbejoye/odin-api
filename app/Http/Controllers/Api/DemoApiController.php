<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DemoApiController extends Controller
{
    public function storeProduct(Request $request)
    {
        if (!$request->filled('name')) {
            return response()->json([
                'error' => 'Product name is required',
            ], 422);
        }

        return response()->json([
            'message' => 'Product created',
            'name' => $request->input('name'),
        ], 201);
    }

    public function showProduct($id)
    {
        return response()->json([
            'message' => "Product {$id} was not found",
            'status' => 'failed',
        ], 404);
    }

    public function storeOrder(Request $request)
    {
        return response()->json([
            'success' => false,
            'reason' => 'Insufficient inventory',
        ], 409);
    }

    public function profile()
    {
        return response()->json([
            'error_message' => 'Authentication required',
        ], 401);
    }

    public function systemCheck()
    {
        throw new \RuntimeException(
            'Simulated internal processing failure'
        );
    }
}