<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestApiController extends Controller
{
    public function test()
    {
        // return response()->json([
        //     'message' => 'Test API working successfully!',
        // ]);

        return response()->json(data: [
            "status" => "success",
            "message" => "API Work"

        ]);
    }
}
