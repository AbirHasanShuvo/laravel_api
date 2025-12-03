<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::get();

        return response()->json([
            'status' => 'Success',
            'count' => count($comments),
            'data' => $comments

        ], status: 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:blog_posts,id',
            // 'user_id' => 'required|integer',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors()
            ], 400);
        }

        $data['post_id'] = $request->post_id;
        $data['user_id'] = auth()->id(); // Get the authenticated user's ID
        $data['content'] = $request->content;

        Comment::create($data);

        //it will create a new record in the database

        return response()->json([
            'status' => 'Success',
            'message' => 'Comment added & waiting for admin approval'
        ], 201);
    }

    //change comment status
    public function changeStatus(Request $request)
    {
        //validate input

        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:comments,id',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors()
            ], 400);
        }

        $comment = Comment::find($request->comment_id);
        $comment->status = $request->status;
        $comment->save();

        return response()->json([
            'status' => 'Success',
            'message' => 'Comment status changes successfully'
        ]);
    }

    //updated one 

    // public function changeStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required'
    //     ]);

    //     $comment = Comment::findOrFail($id);
    //     $comment->status = $request->status;
    //     $comment->save();

    //     return response()->json([
    //         'status' => 'Success',
    //         'message' => 'Comment status changed successfully'
    //     ]);
    // }







    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comments = Comment::where('post_id', $id)->get();

        if ($comments) {
            return response()->json([
                'status' => 'Success',
                'count' => count($comments),
                'data' => $comments

            ], status: 200);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'No comments found for this post'
            ]);
        }
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
