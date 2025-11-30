<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Seo;
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
        // $posts = BlogPost::get();
        //here i also want data from the table name SEO

        $posts = BlogPost::with('seo_data')->get();

        return response()->json([
            'status' => 'Succes',
            'count' => count($posts),
            'data' => $posts
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'nullable|image|max:2048',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keywords' => 'required'

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
        }
        if ($loggedInUser->role == 'admin' || $loggedInUser->role == 'author')
            $data['published_at'] = now();
        // else {
        //     $data['status'] = 'draft';
        //     // $data['published_at'] = now();
        // }

        //here editing for the SEO


        $blogPost = BlogPost::create($data);

        $postId = $blogPost->id;

        $seoData['post_id'] = $postId;
        $seoData['meta_title'] = $request->meta_title;
        $seoData['meta_description'] = $request->meta_description;
        $seoData['meta_keywords'] = $request->meta_keywords;

        Seo::create($seoData);

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

        //check blog post
        $blogPost = BlogPost::find($id);

        if (!$blogPost) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'No Blogpost found'
            ]);
        }


        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'title' => 'required',
            'content' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors(),
            ], 400);
        }

        //check if user is same as logged in user

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

        $blogPost->category_id = $request->category_id;
        $blogPost->user_id = $request->user_id;
        $blogPost->title = $request->title;
        $blogPost->slug = Str::slug($request->title);

        $blogPost->content = $request->content;
        $blogPost->excerpt =  $request->excerpt;
        $blogPost->status =  $request->status;

        $blogPost->save();

        //it will update on the database

        return response()->json([
            'status' => 'Updated the blog post',
            'message' => 'Blog post edited succesfully'
        ], status: 201);
    }

    public function blogPostImage(Request $request, int $id)
    {
        //check blog post
        $blogPost = BlogPost::find($id);

        if (!$blogPost) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'No Blogpost found'
            ]);
        }


        //validation checking
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
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

        // upload image
        $imagePath = null;

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/posts'), $filename);
            $imagePath = 'storage/posts/' . $filename;
        }

        $blogPost->thumbnail = isset($imagePath) ? $imagePath : $blogPost->thumbnail;
        $blogPost->save();

        return response()->json([
            'status' => 'Succes',
            'message' => 'Blog image updated succesfully'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blogPost = BlogPost::find($id);

        if (!$blogPost) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'No Blogpost found'
            ], status: 404);
        }

        $loggedInUser = Auth::user();

        if ($loggedInUser->id == $blogPost->user_id || Auth::user()->role == 'admin') {
            $blogPost->delete();

            return response()->json([
                'status' => 'Succes',
                'message' => 'Blogpost deleted succesfully'
            ], 201);
        } else {
            return response()->json([
                'status' => 'Fail',
                'message' => 'You are not allowed to perform this task'
            ], 403);
        }
    }
}
