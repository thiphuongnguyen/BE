<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'admin_name' => 'required|string',
            'admin_password' => 'required|string',
        ]);

        // Check if admin_name already exists
        $existingAdmin = Admin::where('admin_name', $request->input('admin_name'))->first();

        if ($existingAdmin) {
            // If admin_name exists, return an error response
            throw ValidationException::withMessages(['admin_name' => 'Tên admin đã tồn tại trong hệ thống.']);
        }

        // Create a new admin
        $admin = Admin::create([
            'admin_name' => $request->input('admin_name'),
            'admin_password' => Hash::make($request->input('admin_password')),
        ]);

        // Return a JSON response with the admin data and 201 Created status code
        return response()->json([
            'message' => 'admin created successfully',
            'admin' => $admin,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        //
    }

    public function login(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string',
            'admin_password' => 'required|string',
        ]);
    
        try {
            // Attempt to authenticate the admin
            $admin = Admin::where('admin_name', $request->input('admin_name'))->firstOrFail();
    
            if (!Hash::check($request->input('admin_password'), $admin->admin_password)) {
                // Authentication failed
                throw new \Exception('The provided credentials are incorrect.');
            }

            return response()->json([
                'message' => 'Customer authenticated successfully',
                'admin' => $admin,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Authentication failed',
                'data' => [],
            ]);
        }
    }
}
