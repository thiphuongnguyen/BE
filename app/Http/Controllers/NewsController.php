<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = 16;
        $news = News::paginate($perPage);

        return response()->json($news);
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
            'news_name' => 'required|string',
            'news_content' => 'required|string',
            'news_image' => 'required|string',
            'news_status' => 'required|in:1,0',
        ];

        $request->validate($rules);

        // Create the news if validation passes
        $news = News::create([
            'news_name' => $request->input('news_name'),
            'news_content' => $request->input('news_content'),
            'news_image' => $request->input('news_image'),
            'news_status' => $request->input('news_status'),
        ]);

        return response()->json(['message' => 'News created successfully', 'data' => $news]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function show(News $news)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function edit(News $news)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'news_name' => 'string',
            'news_content' => 'string',
            'news_image' => 'string',
            'news_status' => 'in:1,0',
        ]);

        // Update news
        $news->update([
            'news_name' => $request->input('news_name'),
            'news_content' => $request->input('news_content'),
            'news_image' => $request->input('news_image'),
            'news_status' => $request->input('news_status'),
        ]);

        return response()->json(['message' => 'News updated successfully', 'data' => $news]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $news)
    {
        $news->delete();
        return response()->json(['message' => 'News deleted successfully']);
    }

    public function getNewsDetail($newsId)
    {
        try {
            // Lấy ra chi tiết của news với $newsId
            $news = News::findOrFail($newsId);

            // Trả về dữ liệu JSON chứa chi tiết của news
            return response()->json($news);

        } catch (\Exception $e) {
            // Xử lý nếu không tìm thấy news
            return response()->json(['error' => 'News not found'], 404);
        }
    }

    public function getAllNewsInactive()
    {
        $perPage = 16;
        $news = News::where('news_status', '!=', 0)->paginate($perPage);

        return response()->json($news);
    }

    public function updateNewsStatus(Request $request, News $news)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'news_status' => 'required|in:0,1',
        ]);

        $news->update([
            'news_status' => $request->input('news_status'),
        ]);

        return response()->json(['message' => 'News status updated successfully']);
    }
}
