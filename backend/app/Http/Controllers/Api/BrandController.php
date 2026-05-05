<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::withCount('products')->get();

        return response()->json($brands);
    }

    public function show(int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        return response()->json($brand);
    }
}
