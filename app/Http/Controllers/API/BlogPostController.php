<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = BlogPost::get();

        return response()->json([
            'status' => 'Succes',
            'count' => count($posts),
            'data' => $posts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => 'required|numeric',
    //         'catagory_id' => 'required|numeric',
    //         'title' => 'required',
    //         'content' => 'required',
    //         'thumbnail' => 'nullable|image|max:2048'

    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'Failed',
    //             'message' => $validator->errors()
    //         ], 400);
    //     }

    //     //check the logged in user

    //     $loggedInUser = Auth::user();

    //     if ($loggedInUser != $request->user_id) {
    //         return response()->json([
    //             'status' => 'Failed',
    //             'message' => 'Unauthorised access'
    //         ], 400);
    //     }

    //     //check category is valid or not.

    //     $category =  BlogCategory::find($request->category_id);

    //     if (!$category) {
    //         return response()->json([
    //             'status' => 'Failed',
    //             'message' => 'No category found'
    //         ], 404);
    //     }

    //     $imagePath = null;

    //     if ($request->hasFile('thumbnail') && $request->isValid()) {
    //         $file = $request->file('thumbnail');

    //         $filename = time() . '_' . $file('thumbnail');

    //         //move file to the storage
    //         $file->move(public_path('storage/posts'), $filename);

    //         //save image path to the database
    //         $imagePath = 'storage/posts/' . $filename;
    //     }

    //     //now giving the data 
    //     $data['title'] = $request->title;
    //     $data['slug'] = Str::slug($request->slug);
    //     $data['user_id'] = $request->user_id;
    //     $data['category_id'] = $request->category_id;
    //     $data['excerpt'] = $request->excerpt;
    //     $data['thumbnail'] = $imagePath ? $imagePath : null;

    //     if (Auth::user()->role == 'admin') {
    //         $data['status'] = 'published';
    //         $data['published_at'] = date('Y-m-d H:i:s');
    //     }

    //     BlogPost::create($data);

    //     return response()->json([
    //         'status' => 'Success',
    //         'message' => 'Blog post created succesfully'

    //     ], status: 201);
    // }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors()
            ], 400);
        }

        // check logged in user
        $loggedInUser = Auth::user();

        if ($loggedInUser->id != $request->user_id) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Unauthorised access'
            ], 403);
        }

        // check category
        $category = BlogCategory::find($request->category_id);

        if (!$category) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'No category found'
            ], 404);
        }

        // upload image
        $imagePath = null;

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/posts'), $filename);
            $imagePath = 'storage/posts/' . $filename;
        }

        // prepare data
        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'excerpt' => $request->excerpt,
            'thumbnail' => $imagePath,
            'content' => $request->content
        ];

        if ($loggedInUser->role == 'admin') {
            $data['status'] = 'published';
            $data['published_at'] = now();
        } else {
            $data['status'] = 'draft';
            $data['published_at'] = now();
        }

        BlogPost::create($data);

        return response()->json([
            'status' => 'Success',
            'message' => 'Blog post created successfully'
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
