<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = BlogCategory::get();
        return response()->json([
            'status' => 'success',
            'count' => count($categories),
            'data' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), rules: [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'error' => $validator->errors()
            ], 400);
        }

        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->slug);

        BlogCategory::create($data); //create new record in database

        return response()->json([
            'status' => 'Success',
            'message' => 'Category created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors()
            ], 400);
        }

        $category = BlogCategory::find($id);

        if ($category) {
            $category->name = $request->name;
            $category->slug = Str::slug($request->slug);
            $category->save();

            return response()->json([
                'status' => 'Succes',
                'message' => 'Category edited succesfully'
            ], 201);
        } else {
            return response()->json(
                [
                    'status' => 'Failed',
                    'message' => 'No Category found'
                ],
                status: 404
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = BlogCategory::find($id);

        if ($category) {
            BlogCategory::destroy($id); //this will delete the category by id 

            return response()->json([
                'status' => 'Succes',
                'message' => 'Category deleted succesfully'
            ]);
        } else {
            return response()->json(
                [
                    'status' => 'Failed',
                    'message' => 'No Category found'
                ],
                status: 404
            );
        }
    }
}
