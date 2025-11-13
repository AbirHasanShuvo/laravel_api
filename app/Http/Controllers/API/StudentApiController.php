<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::get();
        return response()->json(data: [
            "status" => "success",
            "data" => $students

        ], status: 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(data: $request->all(), rules: [
            "name" => "required|min:4",
            "email" => "required|unique:students,email",
            "gender" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(data: [
                'status' => 'fail',
                'message' => $validator->errors()

            ], status: 400);
        }

        $data = $request->all();
        Student::create(attributes: $data);

        return response()->json(data: [
            "status" => "Success",
            "message" => "Student created successfully"
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
