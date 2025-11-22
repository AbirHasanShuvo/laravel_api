<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

class StudentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::where('name', 'abir')->get();
        return response()->json(data: [
            "status" => "success",
            "data" => $students,

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
        $student = Student::find($id);

        if ($student) {
            return response()->json([
                "status" => "success",
                "data" => $student

            ], 200);
        }

        return response()->json([
            "status" => "fail",
            "message" => "No user found"

        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), rules: [
            "name" => "required|min:4",
            "email" => "required|unique:students,email," . $id,
            "gender" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "fail",
                "message" => $validator->errors()

            ], 400);
        }

        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No student found'
            ], 404);
        }

        $student->name = $request->name;
        $student->email = $request->email;
        $student->gender = $request->gender;
        $student->save(); //it will save the student data which is new and given 

        return response()->json([
            'status' => 'Succcessful',
            'message' => 'Student updated successfully',
            'data' => $student
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                "status" => "fail",
                "message" => "Student not found"
            ], 404);
        }

        $student->delete();

        return response()->json([
            "status" => "Success",
            "message" => "Student deleted successfully",
            "data"

        ], 201);
    }
}
