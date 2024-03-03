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
        $responseData = [
            'data' => $news,
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
}
