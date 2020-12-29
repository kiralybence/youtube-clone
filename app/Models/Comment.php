<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'content',
    ];

    protected $appends = [
        'authUserRating',
        'points',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getPointsAttribute()
    {
        $upvoteCount = DB::table('ratings')
            ->where('comment_id', $this->id)
            ->where('rate_type', 'upvote')
            ->count();

        $downvoteCount = DB::table('ratings')
            ->where('comment_id', $this->id)
            ->where('rate_type', 'downvote')
            ->count();

        return $upvoteCount - $downvoteCount;
    }

    public function rate($rateType)
    {
        // Delete all previous ratings
        DB::table('ratings')
            ->where('user_id', auth()->id())
            ->where('comment_id', $this->id)
            ->delete();

        // Apply new rating
        if ($rateType !== 'neutral') {
            DB::table('ratings')->insert([
                'user_id' => auth()->id(),
                'comment_id' => $this->id,
                'rate_type' => $rateType,
                'created_at' => now(),
            ]);
        }
    }

    public function getAuthUserRatingAttribute()
    {
        $rating = DB::table('ratings')
            ->where('user_id', auth()->id())
            ->where('comment_id', $this->id)
            ->orderByDesc('created_at')
            ->first();

        return $rating->rate_type ?? 'neutral';
    }
}
