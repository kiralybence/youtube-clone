<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request, $videoKey)
    {
        $video = Video::findByUniqueKey($videoKey);

        if (empty($video)) {
            return response()->json(NULL, 404);
        }

        $comments = $video->comments()
            ->with(['user', 'comments', 'comments.user'])
            ->whereNull('comment_id')
            ->orderByDesc('comments.created_at')
            ->get();

        // TODO: don't send unnecessary information
        return response()->json($comments);
    }

    public function store(Request $request, $videoKey)
    {
        $video = Video::findByUniqueKey($videoKey);

        if (empty($video)) {
            return response()->json(NULL, 404);
        }

        // TODO: validate (+ check if parent belongs to the same video)

        $comment = new Comment([
            // TODO: use $comment->content instead
            'content' => $request->get('content'),
        ]);

        $comment->video()->associate($video);
        $comment->user()->associate(auth()->user());

        if ($request->filled('parent')) {
            $parent = Comment::findOrFail($request->parent);
            $comment->comment()->associate($parent);
        }

        $comment->save();

        $comment->load([
            'comments',
        ]);

        return response()->json($comment, 201);
    }

    public function rate(Request $request, Comment $comment) {
        // TODO: validate

        $comment->rate($request->rateType);
    }
}
