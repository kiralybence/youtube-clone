<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function subStatus(User $user)
    {
        return response()->json([
            'status' => auth()->check() && $user->subscribers->contains(auth()->user())
        ]);
    }

    public function subscribe(User $user)
    {
        if (!auth()->check()) {
            abort(401);
        }

        auth()->user()->subscriptions()->attach($user);
    }

    public function unsubscribe(User $user)
    {
        if (!auth()->check()) {
            abort(401);
        }

        auth()->user()->subscriptions()->detach($user);
    }
}
