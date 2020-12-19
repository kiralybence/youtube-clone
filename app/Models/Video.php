<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'unique_key',
        'filename',
    ];

    protected $appends = [
        'viewCount',
        'thumbnailUrl',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'max:255'],
            'description' => ['nullable', 'max:65535'],
            'video' => ['required', 'mimetypes:video/mp4,video/webm'],
        ]);

        return $validator;
    }

    public function incrementViewCount()
    {
        // Check if the user already watched this video in the past 24 hours
        $recentViews = DB::table('views')
            ->where('user_id', auth()->id())
            ->where('video_id', $this->id)
            ->where('ip_address', Request::ip())
            ->where('viewed_at', '>', now()->subRealHours(24))
            ->count();

        if ($recentViews > 0) {
            return false;
        }

        // Increment the view count
        return DB::table('views')->insert([
            'user_id' => auth()->id(),
            'video_id' => $this->id,
            'ip_address' => Request::ip(),
            'viewed_at' => now(),
        ]);
    }

    public function getViewCountAttribute()
    {
        return DB::table('views')
            ->where('video_id', $this->id)
            ->count();
    }

    public function getThumbnailUrlAttribute()
    {
        return asset('/storage/thumbnails/'.$this->unique_key).'.jpg';
    }
}
