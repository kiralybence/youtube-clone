<?php

namespace App\Http\Controllers;

use App\Models\Video;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

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
        $uniqueKey = Str::random(16);

        // Use the unique key as the video's filename
        $filename = $uniqueKey . '.' . $request->video->getClientOriginalExtension();

        // Save the video
        $request->video->storeAs('videos', 'source_'.$filename, 'public');

        // Save the video entry to the database
        // TODO: use "new Video()" instead of create
        auth()->user()->videos()->create([
            'title' => $request->title,
            'description' => $request->description,
            'unique_key' => $uniqueKey,
            'filename' => $filename,
        ]);

        // TODO: create a job for processing the video

        // Redirect the user to the video's page
        return redirect()->route('video.show', ['video' => $uniqueKey]);
    }

    public function show($uniqueKey)
    {
        // Try to find the video by its unique_key
        // TODO: use a findByUniqueKey() method
        $video = Video::with(['user', 'user.subscribers'])->where('unique_key', $uniqueKey)->first();

        // If there's no such video
        if (empty($video)) {
            // Redirect to 404 page
            abort(404);
        }

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
