<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function show($videoKey)
    {
        $video = Video::findByUniqueKey($videoKey);

        if (empty($video)) {
            return response()->json(NULL, 404);
        }

        // TODO: don't return unnecessary information
        return response()->json($video);
    }

    public function rate(Request $request, $videoKey)
    {
        $video = Video::findByUniqueKey($videoKey);

        if (empty($video)) {
            return response()->json(NULL, 404);
        }

        // TODO: validate

        $video->rate($request->rateType);
    }
}
