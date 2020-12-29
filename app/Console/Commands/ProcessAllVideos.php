<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

class ProcessAllVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:allvideos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates multiple resolutions and generates thumbnails of ALL videos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $videos = Video::all();

        foreach ($videos as $video) {
            $video->process();
        }

        $this->info('Successfully processed ' . count($videos) . ' videos.');

        return 0;
    }
}
