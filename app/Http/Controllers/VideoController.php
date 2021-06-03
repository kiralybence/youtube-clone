<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function create()
    {
        return view('video.create');
    }

    public function store(Request $request)
    {
        // Validate the request
        $validator = Video::validate($request);

        // If the validator fails, redirect the user back to the upload form
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        // Generate a unique key
        $uniqueKey = Video::generateUniqueKey();

        // Use the unique key as the video's filename
        $filename = $uniqueKey . '.' . $request->video->getClientOriginalExtension();

        // Save the video
        $request->video->storeAs('videos', 'source_'.$filename, 'public');

        // Save the video entry to the database
        $video = new Video();
        $video->title = $request->title;
        $video->description = $request->description;
        $video->unique_key = $uniqueKey;
        $video->filename = $filename;
        $video->user()->associate(auth()->user());

        // TODO: create a job for processing the video

        // Redirect the user to the video's page
        return redirect()->route('video.show', ['video' => $uniqueKey]);
    }

    public function show($uniqueKey)
    {
        // Try to find the video by its unique_key
        $video = Video::findByUniqueKey($uniqueKey);

        // If there's no such video
        if (empty($video)) {
            // Redirect to 404 page
            abort(404);
        }

        $video->load([
            'user',
            'user.subscribers'
        ]);

        // Increment the video's viewcount
        $video->incrementViewCount();

        // Grab some other videos to recommend
        $recommendedVideos = $video->user->videos()
            ->with('user')
            ->where('id', '<>', $video->id)
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        return view('video.show', [
            'video' => $video,
            'recommendedVideos' => $recommendedVideos,
        ]);
    }
}
