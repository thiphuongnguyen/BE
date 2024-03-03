<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($product_id)
    {
        $galleries = Gallery::where('product_id', $product_id)->get();

        // Kiểm tra xem có galleries nào hay không
        if ($galleries->isEmpty()) {
            return response()->json(['message' => 'No galleries found for the given product_id.'], 404);
        }

        // Trả về danh sách galleries
        return response()->json($galleries, 200);
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
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'gallery_images' => 'required|array',
            'gallery_images.*' => 'url'
        ]);

        // Lấy product_id từ request
        $product_id = $request->input('product_id');

        // Lấy danh sách các đường dẫn ảnh từ request
        $galleryImages = $request->input('gallery_images');

        // Lặp qua từng đường dẫn ảnh và lưu vào CSDL
        foreach ($galleryImages as $imageUrl) {
            $gallery = new Gallery();
            $gallery->product_id = $product_id;
            $gallery->gallery_image = $imageUrl;

            // Lưu gallery vào CSDL
            $gallery->save();
        }

        // Trả về thông báo thành công
        return response()->json(['message' => 'Multiple galleries have been added successfully.'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show(Gallery $gallery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(Gallery $gallery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gallery $gallery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gallery $gallery)
    {
        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found.'], 404);
        }
    
        // Xóa ảnh và xóa gallery từ CSDL
        $gallery->delete();
    
        // Trả về thông báo thành công
        return response()->json(['message' => 'Gallery image deleted successfully.'], 200);
    }
}
