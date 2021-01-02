<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $latestGlobal = Video::with('user')->orderByDesc('created_at')->limit(8)->get();
        $latestSub = Video::with('user')->whereHas('user.subscribers', function(Builder $query) {
            $query->where('id', auth()->id());
        })->orderByDesc('created_at')->limit(8)->get();

        return view('home', [
            'latestGlobal' => $latestGlobal,
            'latestSub' => $latestSub,
        ]);
    }

    public function search(Request $request)
    {
        // If no search query was given, redirect the user back
        if (empty($request->q)) {
            return back();
        }

        $videos = Video::with('user')->where('title', 'LIKE', '%'.$request->q.'%')->limit(6)->get();
        $users = User::where('name', 'LIKE', '%'.$request->q.'%')->limit(6)->get();

        return view('search', [
            'videos' => $videos,
            'users' => $users,
        ]);
    }
}
