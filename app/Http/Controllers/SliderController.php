<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Slider::select('slider_name', 'slider_image', 'slider_status')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Hiển thị biểu mẫu tạo mới (nếu cần)
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'slider_name' => 'required|string|max:255',
            'slider_image' => 'required|url',
            'slider_status' => 'required|in:1,0',
        ]);

        // Lưu slider mới vào cơ sở dữ liệu
        $slider = Slider::create($validatedData);

        // Có thể thêm thông báo hoặc chuyển hướng ở đây nếu cần

        return response()->json($slider, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $slider)
    {
        // Hiển thị biểu mẫu chỉnh sửa (nếu cần)
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slider $slider)
    {
        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'slider_name' => 'required|string|max:255',
            'slider_image' => 'required|url',
            'slider_status' => ['required', Rule::in(['1', '0'])],
        ]);

        // Cập nhật thông tin của slider
        $slider->update($validatedData);

        // Có thể thêm thông báo hoặc chuyển hướng ở đây nếu cần

        return response()->json($slider, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slider $slider)
    {
        if ($slider) {
            $slider->delete();

            // Trả về phản hồi với thông báo
            return response()->json(['message' => 'Slider deleted successfully'], 200);
        } else {
            // Trả về phản hồi nếu slider không tồn tại
            return response()->json(['message' => 'Slider not found'], 404);
        }
    }
}
