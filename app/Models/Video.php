<?php

namespace App\Models;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\FrameRate;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class Video extends Model
{
    use HasFactory;

    // TODO: remove all fillables, because we're not using create or update methods anymore
    protected $fillable = [
        'title',
        'description',
        'unique_key',
        'filename',
        'processed_at',
    ];

    protected $appends = [
        'viewCount',
        'authUserRating',
        'rating',

        // URL
        'thumbnailUrl',
        'videoUrls',
        'previewUrl',

        // TODO: don't expose paths to frontend

        // Dir path
        'videoStorageDirPath',
        'thumbnailStorageDirPath',
        'previewStorageDirPath',

        // File path
        'videoStoragePaths',
        'thumbnailStoragePath',
        'previewStoragePath',
    ];

    const RESOLUTIONS = [
        [
            'height' => 144,
            'framerate' => 10,
            'bitrate' => 100,
            'audioBitrate' => 32,
        ],
        [
            'height' => 240,
            'framerate' => 15,
            'bitrate' => 350,
            'audioBitrate' => 64,
        ],
        [
            'height' => 360,
            'framerate' => 24,
            'bitrate' => 500,
            'audioBitrate' => 96,
        ],
        [
            'height' => 480,
            'framerate' => 24,
            'bitrate' => 1000,
            'audioBitrate' => 96,
        ],
        [
            'height' => 720,
            'framerate' => 30,
            'bitrate' => 3500,
            'audioBitrate' => 128,
        ],
        [
            'height' => 1080,
            'framerate' => 30,
            'bitrate' => 6000,
            'audioBitrate' => 128,
        ],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'max:255'],
            'description' => ['nullable', 'max:65535'],
            'video' => ['required', 'mimetypes:video/mp4'],
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
        return file_exists($this->thumbnailStoragePath)
            ? asset('/storage/thumbnails/'.$this->unique_key.'.jpg')
            : NULL; // TODO: placeholder thumbnail
    }

    public function getVideoUrlsAttribute()
    {
        $urls = [];

        foreach (self::RESOLUTIONS as $resolution) {
            $path = $this->getVideoStoragePath($resolution['height']);

            if (file_exists($path)) {
                $urls[$resolution['height']] = $this->getVideoUrl($resolution['height']);
            }
        }

        $urls['source'] = $this->getVideoUrl('source');

        return $urls;
    }

    public function getVideoUrl($res = 'source')
    {
        return asset('/storage/videos/'.$res.'_'.$this->filename);
    }

    public function getVideoStorageDirPathAttribute()
    {
        return storage_path('app/public').'/videos/';
    }

    public function getThumbnailStorageDirPathAttribute()
    {
        return storage_path('app/public').'/thumbnails/';
    }

    public function getPreviewStorageDirPathAttribute()
    {
        return storage_path('app/public').'/previews/';
    }

    public function getVideoStoragePathsAttribute()
    {
        $paths = [];

        foreach (self::RESOLUTIONS as $resolution) {
            $path = $this->getVideoStoragePath($resolution['height']);

            if (file_exists($path)) {
                $paths[$resolution['height']] = $path;
            }
        }

        $paths['source'] = $this->getVideoStoragePath('source');

        return $paths;
    }

    // TODO: are all these paths really necessary?

    public function getVideoStoragePath($res = 'source')
    {
        return $this->videoStorageDirPath.$res.'_'.$this->filename;
    }

    public function getThumbnailStoragePathAttribute()
    {
        return $this->thumbnailStorageDirPath.$this->unique_key.'.jpg';
    }

    public function getPreviewStoragePathAttribute()
    {
        return $this->previewStorageDirPath.$this->unique_key.'.gif';
    }

    public function getPreviewUrlAttribute()
    {
        return file_exists($this->previewStoragePath)
            ? asset('/storage/previews/'.$this->unique_key.'.gif')
            : NULL;
    }

    public function process()
    {
        // Check if video exists
        if (!file_exists($this->videoStoragePaths['source'])) {
            return false;
        }

        // Initialize ffmpeg
        $ffmpeg = FFMpeg::create(array(
            'ffmpeg.binaries'  => env('FFMPEG_PATH'),
            'ffprobe.binaries' => env('FFPROBE_PATH'),
            'timeout'          => 3600, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ));

        // If thumbnails directory doesn't exist, create it
        // This is only needed because ffmpeg cannot save to a non-existing directory
        if (!file_exists($this->thumbnailStorageDirPath)) {
            mkdir($this->thumbnailStorageDirPath);
        }

        // Open video with ffmpeg
        $processed = $ffmpeg->open($this->videoStoragePaths['source']);

        // Initialize codec
        $codec = new X264('libmp3lame', 'libx264');

        // Generate a thumbnail
        $processed
            ->frame(TimeCode::fromSeconds(0))
            ->save($this->thumbnailStoragePath);

        // Optimize the thumbnail
        Image::make($this->thumbnailStoragePath)
            ->fit(1280, 720)
            ->save($this->thumbnailStoragePath, 50, 'jpg');

        // Get the resolution of the source video
        $dimensions = $processed->getStreams()->videos()->first()->getDimensions();
        $aspectRatio = $dimensions->getWidth() / $dimensions->getHeight();

        // Update the source_res
        $this->source_res = $dimensions->getWidth() . 'x' . $dimensions->getHeight();

        // Generate a video for each smaller resolutions
        foreach (self::RESOLUTIONS as $resolution) {
            $width = round($aspectRatio * $resolution['height']);
            if ($width % 2 === 1) $width++; // For some reason ffmpeg doesn't accept odd numbers
            $height = $resolution['height'];
            $framerate = $resolution['framerate'];

            // If the source video is bigger than the target resolution
            if ($dimensions->getHeight() > $height) {
                $processed
                    ->filters()
                    ->resize(new Dimension($width, $height))
                    ->framerate(new FrameRate($framerate), 250)
                    ->synchronize();
                $codec
                    ->setKiloBitrate($resolution['bitrate'])
                    ->setAudioKiloBitrate($resolution['audioBitrate']);
                $processed
                    ->save($codec, $this->videoStorageDirPath . $height . '_' . $this->filename);
            }
        }

        // If previews directory doesn't exist, create it
        // This is only needed because ffmpeg cannot save to a non-existing directory
        if (!file_exists($this->previewStorageDirPath)) {
            mkdir($this->previewStorageDirPath);
        }

        // Generate a preview
        $preview = $ffmpeg->open($this->getVideoStoragePath(360));
        $preview
            ->gif(TimeCode::fromSeconds(0), $preview->getStreams()->videos()->first()->getDimensions(), 10)
            ->save($this->previewStoragePath);

        // Update the processed_at date
        $this->processed_at = now();
        $this->save();

        return $this;
    }

    public function getRatingAttribute()
    {
        $likeCount = DB::table('ratings')
            ->where('video_id', $this->id)
            ->where('rate_type', 'like')
            ->count();

        $dislikeCount = DB::table('ratings')
            ->where('video_id', $this->id)
            ->where('rate_type', 'dislike')
            ->count();

        return [
            'likes' => $likeCount,
            'dislikes' => $dislikeCount,
        ];
    }

    public function rate($rateType)
    {
        // Delete all previous ratings
        DB::table('ratings')
            ->where('user_id', auth()->id())
            ->where('video_id', $this->id)
            ->delete();

        // Apply new rating
        if ($rateType !== 'neutral') {
            DB::table('ratings')->insert([
                'user_id' => auth()->id(),
                'video_id' => $this->id,
                'rate_type' => $rateType,
                'created_at' => now(),
            ]);
        }
    }

    public function getAuthUserRatingAttribute()
    {
        if (!auth()->check()) {
            return 'neutral';
        }

        $rating = DB::table('ratings')
            ->where('user_id', auth()->id())
            ->where('video_id', $this->id)
            ->orderByDesc('created_at')
            ->first();

        return $rating->rate_type ?? 'neutral';
    }

    public static function findByUniqueKey($uniqueKey)
    {
        return Video::query()
            ->where('unique_key', $uniqueKey)
            ->first();
    }

    public static function generateUniqueKey()
    {
        do {
            $uniqueKey = Str::random(16);
        } while (!empty(Video::findByUniqueKey($uniqueKey)));

        return $uniqueKey;
    }
}
