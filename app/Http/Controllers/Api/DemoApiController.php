<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DemoApiController extends Controller
{
    public function storeProduct(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
    ]);

    return response()->json([
        'message' => 'Product created',
        'name' => $validated['name'],
    ], 201);
}

    public function showProduct($id)
    {
         throw new NotFoundHttpException('Product not found');
    }

    public function storeOrder(Request $request)
{
    throw new BusinessException(
        message: 'Insufficient inventory',
        errorCode: 'INSUFFICIENT_STOCK',
        httpStatus: 409
    );
}

    public function profile()
    {
        throw new \Illuminate\Auth\AuthenticationException(
            message: 'User is not authenticated'
        );
    }

    public function systemCheck()
    {
        throw new \RuntimeException(
            'Simulated internal processing failure'
        );
    }
}