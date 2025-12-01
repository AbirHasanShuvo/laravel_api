<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    //Likes and Dislikes



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:blog_posts,id',
            'status' => 'required|integer'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => $validator->errors()
            ], 400);
        }

        $userId = auth()->user()->id;
        $postId = $request->post_id;
        $status = $request->status; // 1 for like, 0 for dislike

        //if user has already reacted to the post, update the reaction
        $like = Like::where('user_id', $userId)->where('post_id', $postId)->first();

        if ($like) {
            if ($like->status == $status) {
                //same reaction, remove it
                $like->delete();

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Reaction removed successfully'
                ], 201);
            } else {
                $like->status = $status;
                $like->save();
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Reaction updated successfully'
                ], 201);
            }
        } else {
            Like::create(
                [
                    'user_id' => $userId,
                    'post_id' => $postId,
                    'status' => $status
                ]
            );

            return response()->json([
                'status' => 'Success',
                'message' => 'Reaction added'
            ], 201);
        }






        // TODO: Store the like/dislike record for the user and post
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
