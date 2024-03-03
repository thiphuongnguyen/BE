<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // lấy all
        $categories = Category::get();
        return response()->json($categories);
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
        $rules = [
            'category_name' => 'required|string',
            'category_desc' => 'nullable|string',
            'category_status' => 'required|in:1,0',
        ];

        $request->validate($rules);

        // Create the category if validation passes
        $category = Category::create([
            'category_name' => $request->input('category_name'),
            'category_desc' => $request->input('category_desc'),
            'category_status' => $request->input('category_status'),
        ]);

        return response()->json(['message' => 'Category created successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        // Kiểm tra xem sản phẩm tồn tại không
        if (!$category) {
            return response()->json(['message' => 'category not found'], 404);
        }
        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255',
            'category_desc' => 'nullable|string',
            'category_status' => [Rule::in(['1', '0'])],
        ]);

        // Cập nhật thông tin của slider
        $category->update($validatedData);

        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if (!$category) {
            return response()->json(['message' => 'category not found'], 404);
        }

        // Xóa tất cả các bản ghi liên quan trong các bảng
        $category->products()->delete();

        // Xóa sản phẩm chính
        $category->delete();

        // Trả về phản hồi với thông báo
        return response()->json(['message' => 'Product and related records deleted successfully'], 200);
    }
}
