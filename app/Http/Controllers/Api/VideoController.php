<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function show($videoKey)
    {
        $video = Video::where('unique_key', $videoKey)->firstOrfail();
        // TODO: don't return unnecessary information
        return response()->json($video);
    }

    public function rate(Request $request, $videoKey)
    {
        $video = Video::where('unique_key', $videoKey)->firstOrfail();

        // TODO: validate

        $video->rate($request->rateType);
    }
}
