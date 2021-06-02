<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function subStatus(User $user)
    {
        if (!auth()->check()) {
            // TODO: don't duplicate code, just set $status to false and put the other checking in an else statment
            return response()->json(['status' => false]);
        }

        $status = $user->subscribers->contains(auth()->user());

        return response()->json(['status' => $status]);
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
