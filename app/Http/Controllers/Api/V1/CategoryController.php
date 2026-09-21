<?php

namespace App\Http\Controllers\Api\V1;

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
        $categories= category::latest()->paginate(15);
        return response()->jaon(
            ['success'=>true,
            'data' => new CategoryResource($categories)],
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
         $category = Category::create($request->validated())
          if($category){
            return response()->json([
                "success"=> true,
                "message"=> "Store Category Successfuly",
                "data" => new CategoryResource($category),</a>
            ],201);
          }else{
            return response()->json([
                "success"=> false,
                "message"=> "Store Category Failed",
            ],
            404
        );  
          }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::where('id',$id)->first();
        if($category){
            return response()->json([
                'success'=>true,
                'data' => new CategoryResource($category)],
                200
            );
        }else{
            return response()->json([
                'success'=>false,
                'message' => 'Category not found',
            ],
            404
        );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        $category = Category::where('id',$id)->first();
        if($category){
            $category->update($request->validated());
            return response()->json(
                [
                    'date' => new CategoryResource($category),
                    "sucess" => true,
                    "message" => "update successfull"
                ],
                200
            );
        } else {
            return response()->json([
                "success" => false,
                "message" => "update category failed",
            ],404);
        }  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::where('id',$id)->first();
        if($category->events->exists()){
            return response()->json([
                "success" => false,
                "message" => "category has events cannot delete",
            ],404);
        }
        if($category){
            $category->delete();
            return response()->json([
                "success" => true,
                "message" => "delete category successfull",
            ],200);
        }else{
            return response()->json([
                "success" => false,
                "message" => "delete category failed",
            ],404);
        }
    }
}
