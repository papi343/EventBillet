<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\StoreCategoryRequest;
use App\Http\Requests\Api\V1\UpdateCategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        if ($category) {
            return response()->json([
                "success" => true,
                "message" => "Category created successfully",
                "data" => new CategoryResource($category),
            ], 201);
        }

        return response()->json([
            "success" => false,
            "message" => "Failed to create category",
        ], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if ($category) {
            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category),
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Category not found',
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->update($request->validated());

            return response()->json([
                "success" => true,
                "message" => "Category updated successfully",
                "data" => new CategoryResource($category),
            ], 200);
        }

        return response()->json([
            "success" => false,
            "message" => "Category not found",
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                "success" => false,
                "message" => "Category not found",
            ], 404);
        }

        if ($category->events()->exists()) {
            return response()->json([
                "success" => false,
                "message" => "Category has events and cannot be deleted",
            ], 422);
        }

        $category->delete();

        return response()->json([
            "success" => true,
            "message" => "Category deleted successfully",
        ], 200);
    }
}
