<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\People;
use function Laravel\Prompts\error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeopleApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $peoples = People::get();
        return response()->json([
            "status" => "Success",
            "data" => $peoples
        ], status: 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(data: $request->all(), rules: [
            "name" => "required|min:4",
            "age" => "required",
            "email" => "required|unique:peoples,email",
            "mobile" => "required",
            "address" => "required",
            "gender" => "required"

        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "Failed to create",
                "message" => $validator->errors()
            ], status: 400);
        }

        $data = request()->all();
        People::create(attributes: $data);

        return response()->json(data: [
            "status" => "Success",
            "message" => "People created successfully"
        ], status: 201);
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
