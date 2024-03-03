<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = 16;
        $customer = Customers::paginate($perPage);
        $responseData = [
            'data' => $customer,
        ];

        return response()->json($responseData);
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
            'customer_name' => 'required|string',
            'customer_password' => 'required|string',
        ]);

        // Check if customer_name already exists
        $existingCustomer = Customers::where('customer_name', $request->input('customer_name'))->first();

        if ($existingCustomer) {
            // If customer_name exists, return an error response
            throw ValidationException::withMessages(['customer_name' => 'Tên khách hàng đã tồn tại trong hệ thống.']);
        }

        // Create a new customer
        $customer = Customers::create([
            'customer_name' => $request->input('customer_name'),
            'customer_password' => Hash::make($request->input('customer_password')),
        ]);

        // Generate a personal access token
        $token = $customer->createToken('customer-access-token')->plainTextToken;

        // Update the customer record with the generated token
        $customer->update(['customer_token' => $token]);

        // Return a JSON response with the customer data and access token
        return response()->json([
            'message' => 'Customer created successfully',
            'data' => [
                'customer' => $customer,
                'access_token' => $token,
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function show(Customers $customers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function edit(Customers $customers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customers $customer)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_password' => 'required|string',
            'customer_phone' => 'nullable|numeric',
        ]);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        try {
            $customer->update([
                'customer_name' => $request->input('customer_name'),
                'customer_password' => Hash::make($request->input('customer_password')),
                'customer_phone' => $request->input('customer_phone') ?? null,
            ]);
        
            return response()->json(['message' => 'Customer updated successfully', 'data' => $customer]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating customer', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customers $customers)
    {
        if ($customers) {
            $customers->delete();

            // Trả về phản hồi với thông báo
            return response()->json(['message' => 'customers deleted successfully'], 200);
        } else {
            // Trả về phản hồi nếu customers không tồn tại
            return response()->json(['message' => 'customers not found'], 404);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'customer_password' => 'required|string',
        ]);
    
        try {
            // Attempt to authenticate the customer
            $customer = Customers::where('customer_name', $request->input('customer_name'))->firstOrFail();
    
            if (!Hash::check($request->input('customer_password'), $customer->customer_password)) {
                // Authentication failed
                throw new \Exception('The provided credentials are incorrect.');
            }

            $token = $customer->createToken('customer-access-token')->plainTextToken;

            return response()->json([
                'message' => 'Customer authenticated successfully',
                'data' => [
                    'customer' => $customer,
                    'access_token' => $token,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Authentication failed',
                'data' => [],
            ]);
        }
    }
}
