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
        $request->video->storeAs('videos', $filename, 'public');

        // Initialize ffmpeg
        $ffmpeg = FFMpeg::create(array(
            'ffmpeg.binaries'  => env('FFMPEG_PATH'),
            'ffprobe.binaries' => env('FFPROBE_PATH'),
            'timeout'          => 3600, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ));

        // If thumbnails directory doesn't exist, create it
        // This is only needed because ffmpeg cannot save to a non-existing directory
        if (!file_exists(storage_path('app/public').'/thumbnails/')) {
            mkdir(storage_path('app/public').'/thumbnails/');
        }

        // Generate a thumbnail
        $ffmpeg
            ->open(storage_path('app/public').'/videos/'.$filename)
            ->frame(TimeCode::fromSeconds(0))
            ->save(storage_path('app/public').'/thumbnails/'.$uniqueKey.'.jpg');

        // Optimize the thumbnail
        Image::make(storage_path('app/public').'/thumbnails/'.$uniqueKey.'.jpg')
            ->fit(1280, 720)
            ->save(storage_path('app/public').'/thumbnails/'.$uniqueKey.'.jpg', 50, 'jpg');

        // Save the video entry to the database
        auth()->user()->videos()->create([
            'title' => $request->title,
            'description' => $request->description,
            'unique_key' => $uniqueKey,
            'filename' => $filename,
        ]);

        // Redirect the user to the video's page
        return redirect()->route('video.show', ['video' => $uniqueKey]);
    }

    public function show($uniqueKey)
    {
        // Try to find the video by its unique_key
        $video = Video::with('user')->where('unique_key', $uniqueKey)->first();

        // If there's no such video
        if (empty($video)) {
            // Redirect to 404 page
            abort(404);
        }

        // Increment the video's viewcount
        $video->incrementViewCount();

        // Grab some other videos to recommend
        $recommendedVideos = $video->user->videos()
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
